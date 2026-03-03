@extends('layouts.app')

@section('header_title', 'Transaction details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-5">
                    <div class="mb-3">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-check-lg text-white fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">Transfer Sent</h3>
                    <div class="text-muted">{{ $transfer->created_at->format('M d, Y • H:i') }}</div>
                </div>

                <div class="list-group list-group-flush border-top border-bottom mb-4">
                    <div class="list-group-item px-0 py-3 border-bottom-0 border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Amount sent</span>
                            <span class="fw-bold fs-5 text-wise-blue">{{ number_format($transfer->amount, 2) }} {{ $transfer->currency }}</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item px-0 py-3 border-bottom-0">
                         <div class="d-flex justify-content-between align-items-start">
                            <span class="text-muted">To</span>
                            <div class="text-end">
                                <div class="fw-bold text-wise-blue">{{ $transfer->recipient_name }}</div>
                                <div class="small text-muted">{{ $transfer->recipient_account_mask }}</div>
                                {{-- If we wanted to show the decrypted full account (security risk in UI but shows it works) --}}
                                {{-- <div class="small text-danger">{{ $transfer->recipient_account }}</div> --}}
                            </div>
                        </div>
                    </div>
                    
                    @if($transfer->note_enc)
                    <div class="list-group-item px-0 py-3 border-bottom-0">
                         <div class="d-flex justify-content-between align-items-start">
                            <span class="text-muted">Reference</span>
                            <div class="text-end fw-bold text-wise-blue">{{ $transfer->note_enc }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="list-group-item px-0 py-3 border-bottom-0">
                         <div class="d-flex justify-content-between align-items-start">
                            <span class="text-muted">Transfer ID</span>
                            <div class="text-end font-monospace small text-muted">#{{ $transfer->id }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-3">
                    <a href="{{ route('send-money') }}" class="btn btn-wise-green btn-lg">Send money</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-light text-muted fw-bold">Back to home</a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="#" class="text-decoration-none fw-bold text-wise-blue">
                <i class="bi bi-file-earmark-pdf"></i> Get PDF receipt
            </a>
        </div>
    </div>
</div>
@endsection
