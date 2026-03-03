<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Notifications\TransferSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Auth::user()->transfers()->latest()->paginate(10);
        return view('transfers.index', compact('transfers'));
    }

    // Step 1: Show Form
    public function create()
    {
        $account = Auth::user()->accounts()->first();
        if (!$account) {
            return redirect()->route('dashboard')->with('error', 'No account found.');
        }
        return view('transfers.create', compact('account'));
    }

    // Step 2: Review (Validate and Show Confirmation)
    public function review(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:120',
            'recipient_account' => 'required|string|max:64', // Plaintext input
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'note' => 'nullable|string|max:255',
        ]);

        $account = Auth::user()->accounts()->firstOrFail();

        if ($validated['amount'] > $account->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance.'])->withInput();
        }

        // Pass data to review view (do not save yet)
        return view('transfers.review', ['data' => $validated, 'account' => $account]);
    }

    // Step 3: Store (Create Transfer)
    public function store(Request $request)
    {
        // Re-validate to prevent tampering
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:120',
            'recipient_account' => 'required|string|max:64',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'note' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $account = $user->accounts()->firstOrFail();

        if ($validated['amount'] > $account->balance) {
            return redirect()->route('send-money')->withErrors(['amount' => 'Insufficient balance.']);
        }

        // Idempotency check could be added here, but for MVP we'll just create.
        // The unique constraint on user_id + idempotency_key is in DB.
        // We generate a key if not provided, or use session token as simplistic idempotency for this flow.
        $idempotencyKey = Str::uuid()->toString();

        // Encrypt and Mask
        // Masking: Show last 4 chars
        $mask = str_repeat('*', max(0, strlen($validated['recipient_account']) - 4)) . substr($validated['recipient_account'], -4);

        $transfer = Transfer::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'merchant' => $validated['recipient_name'],
            'method' => 'Wallet',
            'order_number' => $idempotencyKey,
            'fee' => 0,
            'total' => $validated['amount'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_account_mask' => $mask,
            'recipient_account' => $validated['recipient_account'], // Uses virtual attribute mutator for RSA
            'note_enc' => $validated['note'] ?? null, // Casts to encrypted (AES)
            'status' => 'success', // Auto-success for MVP
            'idempotency_key' => $idempotencyKey,
        ]);

        // Deduct balance
        $account->decrement('balance', $validated['amount']);

        // Send Notification
        $user->notify(new TransferSent($transfer));

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transfer successful!');
    }

    public function show(Transfer $transfer)
    {
        // Authorization check
        if ($transfer->user_id !== Auth::id()) {
            abort(403);
        }

        return view('transfers.show', compact('transfer'));
    }
}
