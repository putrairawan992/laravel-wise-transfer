@extends('layouts.app')

@section('header_title', 'Review transfer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Review details of your transfer</h5>
                
                <div class="mb-4 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">You send</div>
                    <div class="display-5 fw-bold text-wise-blue">{{ number_format($data['amount'], 2) }} <span class="text-muted fs-4">{{ $data['currency'] }}</span></div>
                </div>
                
                <div class="d-flex justify-content-center mb-4">
                    <i class="bi bi-arrow-down-circle-fill text-muted fs-3"></i>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Recipient</span>
                        <div class="text-end">
                            <div class="fw-bold text-wise-blue">{{ $data['recipient_name'] }}</div>
                            <div class="small text-muted">Account ending in {{ substr($data['recipient_account'], -4) }}</div>
                        </div>
                    </div>
                    
                    @if(!empty($data['note']))
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Reference</span>
                        <span class="fw-bold text-wise-blue">{{ $data['note'] }}</span>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Fee</span>
                        <span class="fw-bold text-wise-blue">0.00 {{ $data['currency'] }}</span>
                    </div>
                </div>

                <form action="{{ route('send-money.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recipient_name" value="{{ $data['recipient_name'] }}">
                    <input type="hidden" name="recipient_account" value="{{ $data['recipient_account'] }}">
                    <input type="hidden" name="amount" value="{{ $data['amount'] }}">
                    <input type="hidden" name="currency" value="{{ $data['currency'] }}">
                    <input type="hidden" name="note" value="{{ $data['note'] ?? '' }}">
                    
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-wise-green btn-lg">Confirm and send</button>
                        <a href="{{ route('send-money') }}" class="btn btn-light text-muted fw-bold">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center text-muted small mt-3">
            <i class="bi bi-lock-fill"></i> Transfers are subject to review and may be delayed if we need more info.
        </div>
    </div>
</div>
@endsection
