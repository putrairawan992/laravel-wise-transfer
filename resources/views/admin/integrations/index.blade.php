@extends('layouts.admin')

@section('title', 'Admin - Integration Status')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-3 text-white fw-bold">Integration Status</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0" style="background-color: #1e293b;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-white mb-3">Fiuu Payment Gateway</h5>
                            @if($fiuuStatus === 'configured')
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                                    <span class="text-success fw-medium">Configured</span>
                                </div>
                                <p class="text-secondary small">Merchant ID and Verify Key found.</p>
                            @else
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle-fill text-danger fs-4 me-2"></i>
                                    <span class="text-danger fw-medium">Missing Configuration</span>
                                </div>
                                <p class="text-secondary small">Please set RMS_MERCHANT_ID and RMS_VERIFY_KEY in .env.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0" style="background-color: #1e293b;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-white mb-3">Moralis API</h5>
                            @if($moralisStatus === 'configured')
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                                    <span class="text-success fw-medium">Configured</span>
                                </div>
                                <p class="text-secondary small">API Key found.</p>
                            @else
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle-fill text-danger fs-4 me-2"></i>
                                    <span class="text-danger fw-medium">Missing Configuration</span>
                                </div>
                                <p class="text-secondary small">Please set MORALIS_API_KEY in .env.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0" style="background-color: #1e293b;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-white mb-3">WhatsApp API (Twilio)</h5>
                            @if($waStatus === 'configured')
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                                    <span class="text-success fw-medium">Configured</span>
                                </div>
                                <p class="text-secondary small">Twilio Auth SID and Token found.</p>
                            @else
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle-fill text-danger fs-4 me-2"></i>
                                    <span class="text-danger fw-medium">Missing Configuration</span>
                                </div>
                                <p class="text-secondary small">Please set TWILIO_AUTH_SID and TWILIO_AUTH_TOKEN in .env.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
