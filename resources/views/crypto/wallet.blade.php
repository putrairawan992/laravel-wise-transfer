@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-currency-bitcoin fs-2"></i>
                </div>
                <h2 class="fw-bold text-dark mb-2">Crypto Wallet Checker</h2>
                <p class="text-secondary">Check balance of any wallet address using Moralis API</p>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger rounded-3 mb-4">
                            <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('crypto.check') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="chain" class="form-label fw-medium">Blockchain Network</label>
                            <select class="form-select rounded-3 py-2" id="chain" name="chain" required>
                                <option value="eth" {{ old('chain') == 'eth' ? 'selected' : '' }}>Ethereum (ETH)</option>
                                <option value="bsc" {{ old('chain') == 'bsc' ? 'selected' : '' }}>Binance Smart Chain (BSC)</option>
                                <option value="polygon" {{ old('chain') == 'polygon' ? 'selected' : '' }}>Polygon (MATIC)</option>
                                <option value="avalanche" {{ old('chain') == 'avalanche' ? 'selected' : '' }}>Avalanche (AVAX)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label fw-medium">Wallet Address</label>
                            <input type="text" class="form-control rounded-3 py-2" id="address" name="address" placeholder="0x..." value="{{ old('address') }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                            <i class="bi bi-search me-2"></i> Check Balance
                        </button>
                    </form>

                    @if(session('balance'))
                        <div class="mt-4 pt-4 border-top">
                            <h5 class="fw-bold mb-3">Wallet Details</h5>
                            <div class="bg-light rounded-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Address:</span>
                                    <span class="fw-medium text-break">{{ old('address') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary">Balance:</span>
                                    <span class="fs-4 fw-bold text-primary">
                                        {{ session('balance')['formatted'] ?? '0.0000' }} 
                                        <span class="fs-6 text-uppercase">{{ old('chain') == 'eth' ? 'ETH' : (old('chain') == 'bsc' ? 'BNB' : strtoupper(old('chain'))) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
