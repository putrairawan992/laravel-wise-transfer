<?php

namespace App\Services;

use RazerMerchantServices\Payment;
use Illuminate\Http\Request;

class PaymentService
{
    protected $merchantId;
    protected $verifyKey;
    protected $secretKey;
    protected $environment;

    public function __construct()
    {
        $this->merchantId = env('RMS_MERCHANT_ID');
        $this->verifyKey = env('RMS_VERIFY_KEY');
        $this->secretKey = env('RMS_SECRET_KEY');
        $this->environment = env('RMS_ENVIRONMENT', 'sandbox');
    }

    public function createTransaction($orderId, $amount, $billName, $billEmail, $billMobile, $billDesc = '')
    {
        // Wrapper for the library
        // Note: Ensure the library is installed via composer
        if (!class_exists('RazerMerchantServices\Payment')) {
            throw new \Exception('RazerMerchantServices SDK not installed.');
        }

        $rms = new Payment($this->merchantId, $this->verifyKey, $this->secretKey, $this->environment);
        
        return $rms->getPaymentUrl($orderId, $amount, $billName, $billEmail, $billMobile, $billDesc);
    }

    public function verifySignature(Request $request)
    {
        if (!class_exists('RazerMerchantServices\Payment')) {
            throw new \Exception('RazerMerchantServices SDK not installed.');
        }

        $rms = new Payment($this->merchantId, $this->verifyKey, $this->secretKey, $this->environment);
        
        $key = md5($request->tranID.$request->orderid.$request->status.$request->domain.$request->amount.$request->currency);
        
        return $rms->verifySignature($request->paydate, $request->domain, $key, $request->appcode, $request->skey);
    }
}
