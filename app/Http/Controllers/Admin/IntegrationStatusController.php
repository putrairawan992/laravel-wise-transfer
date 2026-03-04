<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\MoralisService;

class IntegrationStatusController extends Controller
{
    public function index()
    {
        // Check Moralis
        $moralisStatus = 'unknown';
        try {
            // Simple ping to Moralis API (using a dummy call or just checking if service instantiates)
            // Ideally we'd make a real lightweight call, but for now we check if env is set
            if (env('MORALIS_API_KEY')) {
                $moralisStatus = 'configured';
            } else {
                $moralisStatus = 'missing_key';
            }
        } catch (\Exception $e) {
            $moralisStatus = 'error';
        }

        // Check Fiuu (Razer)
        $fiuuStatus = 'unknown';
        if (env('RMS_MERCHANT_ID') && env('RMS_VERIFY_KEY')) {
            $fiuuStatus = 'configured';
        } else {
            $fiuuStatus = 'missing_key';
        }

        // Check WhatsApp (Twilio)
        $waStatus = 'unknown';
        if (env('TWILIO_AUTH_SID') && env('TWILIO_AUTH_TOKEN')) {
            $waStatus = 'configured';
        } else {
            $waStatus = 'missing_key';
        }

        return view('admin.integrations.index', compact('moralisStatus', 'fiuuStatus', 'waStatus'));
    }
}
