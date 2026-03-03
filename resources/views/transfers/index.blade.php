@extends('layouts.app')

@section('header_title', 'Transactions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 ps-4 text-muted small fw-bold text-uppercase">Transaction</th>
                                <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Amount</th>
                                <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Request</th>
                                <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Success</th>
                                <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Requested At</th>
                                <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Completed At</th>
                                <th class="border-0 py-3 pe-4 text-end text-muted small fw-bold text-uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-wise-blue">#{{ Str::limit($transfer->id, 10, '') }}</div>
                                    <div class="small text-muted">{{ $transfer->currency }} • {{ $transfer->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-wise-blue">{{ $transfer->currency }} {{ number_format($transfer->amount, 2) }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Requested</span>
                                </td>
                                <td class="py-3">
                                    @if($transfer->status == 'success' || $transfer->status == 'completed')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Success</span>
                                    @elseif($transfer->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Pending</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">{{ ucfirst($transfer->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $transfer->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 text-muted small">
                                    @if($transfer->status == 'success' || $transfer->status == 'completed')
                                        {{ $transfer->updated_at->format('M d, Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-light text-primary fw-bold">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-receipt fs-1 opacity-25"></i>
                                    </div>
                                    <p class="mb-0">No transactions found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transfers->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $transfers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
