<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class AdminWhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $users = User::with('kycProfile')->where('role', 'user')->get()->map(function($user) {
            // Check KYC profile if user mobile is empty
            if (empty($user->mobile) && $user->kycProfile) {
                $user->mobile = $user->kycProfile->contact_number ?? $user->kycProfile->mobile_number;
            }
            return $user;
        });
        
        $logs = WhatsAppLog::with(['user', 'admin'])->latest()->paginate(10);
        return view('admin.whatsapp.index', compact('users', 'logs'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required', // 'all' or user_id
            'message' => 'required|string|max:1000',
        ]);

        $message = $request->message;
        $count = 0;

        if ($request->user_id === 'all') {
            $users = User::with('kycProfile')->where('role', 'user')->get();
            foreach ($users as $user) {
                $mobile = $user->mobile ?? ($user->kycProfile->contact_number ?? ($user->kycProfile->mobile_number ?? null));
                
                if ($mobile) {
                    $user->mobile = $mobile; // Set for helper function
                    $this->sendMessageToUser($user, $message);
                    $count++;
                }
            }
        } else {
            $user = User::with('kycProfile')->findOrFail($request->user_id);
            $mobile = $user->mobile ?? ($user->kycProfile->contact_number ?? ($user->kycProfile->mobile_number ?? null));
            
            if ($mobile) {
                $user->mobile = $mobile; // Set for helper function
                $this->sendMessageToUser($user, $message);
                $count++;
            } else {
                return back()->with('error', 'Selected user does not have a mobile number (Check Profile or KYC).');
            }
        }

        return back()->with('success', "Message sent/queued for {$count} user(s).");
    }

    protected function sendMessageToUser($user, $message)
    {
        $response = $this->whatsAppService->sendMessage($user->mobile, $message);

        WhatsAppLog::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'recipient_number' => $user->mobile,
            'message' => $message,
            'status' => $response['success'] ? 'sent' : 'failed',
            'sid' => $response['sid'] ?? null,
            'error_message' => $response['error'] ?? null,
        ]);
    }
}
