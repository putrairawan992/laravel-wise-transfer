<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function checkout()
    {
        return view('payment.checkout');
    }

    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
        ]);

        $orderId = 'ORD-' . time();
        
        try {
            $paymentUrl = $this->paymentService->createTransaction(
                $orderId, 
                $request->amount, 
                $request->name, 
                $request->email, 
                $request->mobile,
                'Payment for Transfer'
            );

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        // Handle callback from Fiuu
        // In real implementation, you verify signature here
        
        // $isValid = $this->paymentService->verifySignature($request);
        
        // if ($isValid) {
        //     // Update order status
        // }

        return response()->json(['status' => 'OK']);
    }
    
    public function notify(Request $request)
    {
        // Handle notification (server-to-server)
        return response()->echo('OK');
    }
}
