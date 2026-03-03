@extends('layouts.admin')

@section('title', 'Payments')
@section('page_title', 'Payments')
@section('page_subtitle', 'List of all the payments you received from customers')

@section('content')
    <div class="card p-4 border-0 shadow-sm">
        <form method="GET" class="mb-4">
            <div class="d-flex flex-wrap gap-3 align-items-end justify-content-between">
                <div class="d-flex flex-wrap gap-3 flex-grow-1">
                    <div class="d-flex gap-2">
                        <div>
                            <label class="small text-muted fw-bold text-uppercase mb-1">Date Range</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 130px;">
                                <span class="input-group-text border-secondary border-opacity-25 text-secondary" style="background: rgba(255,255,255,0.02);">-</span>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 130px;">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="small text-muted fw-bold text-uppercase mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 140px;">
                            <option value="" class="bg-dark">All Status</option>
                            @foreach(['success', 'pending', 'failed'] as $status)
                                <option value="{{ $status }}" class="bg-dark" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="small text-muted fw-bold text-uppercase mb-1">Filters</label>
                        <div class="d-flex gap-2">
                            <input type="text" name="currency" value="{{ request('currency') }}" placeholder="Currency" class="form-control form-control-sm border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 100px;">
                            <input type="text" name="method" value="{{ request('method') }}" placeholder="Method" class="form-control form-control-sm border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 120px;">
                            <input type="text" name="merchant" value="{{ request('merchant') }}" placeholder="Merchant" class="form-control form-control-sm border-secondary border-opacity-25 text-light" style="background: rgba(255,255,255,0.05); width: 140px;">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-end">
                    <div class="position-relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search transactions..." class="form-control form-control-sm border-secondary border-opacity-25 text-light ps-4" style="background: rgba(255,255,255,0.05); width: 240px;">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary small"></i>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Filter</button>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-dark align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255,255,255,0.03);">
                <thead>
                    <tr class="text-muted small text-uppercase" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <th class="py-3 ps-3">Date</th>
                        <th class="py-3">User</th>
                        <th class="py-3">Details</th>
                        <th class="py-3 text-end">Amount</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 pe-3 text-end">Account</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="ps-3">
                                <div class="fw-medium text-white">{{ $transfer->created_at?->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $transfer->created_at?->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-white small" style="width: 32px; height: 32px;">
                                        {{ substr($transfer->user?->email ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="text-truncate" style="max-width: 150px;">
                                        <div class="text-white small">{{ $transfer->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white small mb-1">{{ $transfer->merchant ?? $transfer->recipient_name }}</div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 rounded-pill fw-normal" style="font-size: 0.7rem;">
                                        {{ $transfer->method ?? 'Wallet' }}
                                    </span>
                                    <span class="small text-muted font-monospace">#{{ Str::limit($transfer->order_number ?? $transfer->id, 8) }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-white">{{ number_format((float) $transfer->amount, 2) }} <span class="small text-muted fw-normal">{{ $transfer->currency }}</span></div>
                                @if($transfer->fee > 0)
                                    <div class="small text-muted" style="font-size: 0.75rem;">+ {{ number_format((float) $transfer->fee, 2) }} fee</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColor = match($transfer->status) {
                                        'success' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} border-opacity-25 rounded-pill px-3">
                                    {{ ucfirst($transfer->status) }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <span class="font-monospace small text-muted px-2 py-1 rounded border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.03);">
                                    {{ $transfer->recipient_account }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox fs-1 text-muted opacity-25 mb-3"></i>
                                    <div class="text-muted">No transactions found</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-3">
            {{ $transfers->links() }}
        </div>
    </div>
@endsection
