@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold">DATA STOCK WALLET</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboards</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Stock Wallet</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    @if($account)
    <div class="col-md-3">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold text-wise-blue">Your {{ $account->currency ?? 'USD' }} account details</div>
                    <div class="small text-muted">
                        {{ $account->bank_name ?? 'Wise Bank' }}, 
                        {{ $account->bank_code ?? 'WISE-123' }}, 
                        {{ $account->account_number ?? '**** ' . substr($account->id, -4) }}
                    </div>
                </div>
                <div class="bg-primary text-white rounded p-2">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-12">
        <div class="alert alert-warning">No account found. Please contact support.</div>
    </div>
    @endif
    <!-- Placeholder cards based on image -->
    <div class="col-md-3">
         <div class="card p-3 h-100">
            <div class="d-flex gap-3 align-items-center mb-2">
                <div class="bg-warning rounded-circle p-2" style="width:32px;height:32px;"></div>
                <div>
                    <div class="fw-bold small">MTN</div>
                    <div class="text-muted small" style="font-size:0.7rem;">SME</div>
                </div>
            </div>
            <div class="fs-4 fw-bold">98.50GB</div>
         </div>
    </div>
    <div class="col-md-3">
         <div class="card p-3 h-100">
            <div class="d-flex gap-3 align-items-center mb-2">
                <div class="bg-warning rounded-circle p-2" style="width:32px;height:32px;"></div>
                <div>
                    <div class="fw-bold small">MTN</div>
                    <div class="text-muted small" style="font-size:0.7rem;">CORP GIFTING</div>
                </div>
            </div>
            <div class="fs-4 fw-bold">1007.00GB</div>
         </div>
    </div>
    <div class="col-md-3">
         <div class="card p-3 h-100">
            <div class="d-flex gap-3 align-items-center mb-2">
                <div class="bg-danger rounded-circle p-2" style="width:32px;height:32px;"></div>
                <div>
                    <div class="fw-bold small">AIRTEL</div>
                    <div class="text-muted small" style="font-size:0.7rem;">CORP GIFTING</div>
                </div>
            </div>
            <div class="fs-4 fw-bold">0.00GB</div>
         </div>
    </div>
</div>

<!-- Recent Transfers -->
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Recent Transfers</h5>
        <a href="{{ route('send-money') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Send Money
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Recipient</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $transfer->recipient_name }}</td>
                        <td>{{ $transfer->recipient_account_mask }}</td>
                        <td>{{ $transfer->currency }} {{ number_format($transfer->amount, 2) }}</td>
                        <td>
                            @if($transfer->status == 'completed' || $transfer->status == 'success')
                                <span class="badge bg-success bg-opacity-10 text-success">Success</span>
                            @elseif($transfer->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">Failed</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-light btn-sm text-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No transfers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white text-muted small">
        Showing {{ $transfers->count() }} recent items
    </div>
</div>
@endsection
