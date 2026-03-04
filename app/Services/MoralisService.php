<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoralisService
{
    protected $apiKey;
    protected $baseUrl = 'https://deep-index.moralis.io/api/v2';

    public function __construct()
    {
        $this->apiKey = env('MORALIS_API_KEY');
    }

    public function getWalletBalance($address, $chain = 'eth')
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'accept' => 'application/json',
        ])->get("{$this->baseUrl}/{$address}/balance", [
            'chain' => $chain,
        ]);

        return $response->json();
    }

    public function getWalletTransactions($address, $chain = 'eth')
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'accept' => 'application/json',
        ])->get("{$this->baseUrl}/{$address}", [
            'chain' => $chain,
        ]);

        return $response->json();
    }
}
