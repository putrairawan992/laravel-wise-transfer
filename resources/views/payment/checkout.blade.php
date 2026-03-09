@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Top Up Wallet</h3>
                <p class="text-muted">Secure payment via Fiuu Gateway</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 border-0">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-credit-card-2-front fs-4"></i>
                        <h5 class="mb-0 fw-bold">Payment Details</h5>
                    </div>
                </div>

                <div class="card-body p-5">
                    @if (session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('payment.process') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold text-secondary small text-uppercase">Top Up Amount</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0 fw-bold text-muted ps-3">{{ auth()->user()->currency ?? 'NGN' }}</span>
                                <input type="number" class="form-control border-0 fs-4 fw-bold ps-2 shadow-none" id="amount" name="amount" step="0.01" placeholder="0.00" required autofocus>
                            </div>
                            <div class="form-text text-muted ps-1">Enter the amount you want to add to your wallet.</div>
                        </div>

                        <hr class="border-secondary border-opacity-10 my-4">

                        <h6 class="fw-bold text-dark mb-3">Payer Information</h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label text-muted small fw-semibold">Name</label>
                                <input type="text" class="form-control bg-light border-0" id="name" name="name" value="{{ auth()->user()->name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-muted small fw-semibold">Email</label>
                                <input type="email" class="form-control bg-light border-0" id="email" name="email" value="{{ auth()->user()->email ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="mobile" class="form-label fw-bold text-secondary small text-uppercase">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-phone"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="mobile" name="mobile" placeholder="e.g. +6281234567890" required value="{{ auth()->user()->mobile ?? '' }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill fw-bold shadow-sm hover-scale">
                            <i class="bi bi-lock-fill me-2"></i> Pay Securely with Fiuu
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted"><i class="bi bi-shield-check me-1"></i> Your transaction is encrypted and secure.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>
@endsection
