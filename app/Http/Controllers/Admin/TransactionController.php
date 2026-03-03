<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::query()->with(['user'])->latest();

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%")
                    ->orWhere('recipient_name', 'like', "%{$q}%")
                    ->orWhere('merchant', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->string('currency'));
        }

        if ($request->filled('method')) {
            $query->where('method', $request->string('method'));
        }

        if ($request->filled('merchant')) {
            $query->where('merchant', 'like', '%'.trim((string) $request->string('merchant')).'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('admin.transactions.index', compact('transfers'));
    }
}
