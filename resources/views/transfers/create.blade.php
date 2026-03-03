@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold">Send Money</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboards</a></li>
            <li class="breadcrumb-item active" aria-current="page">Send Money</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Transfer Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('send-money.review') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Recipient Name</label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipient_account" class="form-label">Recipient Account / IBAN</label>
                        <input type="text" class="form-control" id="recipient_account" name="recipient_account" value="{{ old('recipient_account') }}" required>
                        <div class="form-text text-muted"><i class="bi bi-shield-lock"></i> We will store this encrypted; only masked values are shown later.</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" min="0.01" max="{{ $account->balance }}" required>
                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency" readonly>
                                <option value="{{ $account->currency }}" selected>{{ $account->currency }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="note" class="form-label">Note (Optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Review Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-primary text-white p-4">
            <h5 class="mb-4">Available Balance</h5>
            <div class="fs-2 fw-bold mb-1">{{ $account->currency }} {{ number_format($account->balance, 2) }}</div>
            <div class="small opacity-75">Updated just now</div>
        </div>
        
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Security Tips</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2">Never share your password or OTP.</li>
                <li class="mb-2">Verify recipient details before sending.</li>
                <li>Transfers are processed securely using AES encryption.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
