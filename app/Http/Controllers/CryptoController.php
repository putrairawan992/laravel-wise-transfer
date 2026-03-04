<?php

namespace App\Http\Controllers;

use App\Services\MoralisService;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    protected $moralisService;

    public function __construct(MoralisService $moralisService)
    {
        $this->moralisService = $moralisService;
    }

    public function index()
    {
        return view('crypto.wallet');
    }

    public function checkWallet(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'chain' => 'required|string',
        ]);

        try {
            $balance = $this->moralisService->getWalletBalance($request->address, $request->chain);
            
            // Format balance (Wei to Ether/BNB/Matic)
            if (isset($balance['balance'])) {
                $balance['formatted'] = number_format($balance['balance'] / 1000000000000000000, 4);
            }

            return back()->with('balance', $balance)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch wallet data. Please check your API Key and the wallet address.')->withInput();
        }
    }
}
