@extends('layouts.admin')

@section('title', 'e-KYC')
@section('page_title', 'e-KYC')
@section('page_subtitle', 'Submitted KYC profiles')

@section('content')
    <div class="card p-4 border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-dark align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255,255,255,0.03);">
                <thead>
                    <tr class="small text-uppercase" style="color: rgba(226, 232, 240, 0.6); border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <th class="py-3 ps-3">User</th>
                        <th class="py-3">Full Name</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Submitted</th>
                        <th class="py-3 pe-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kycs as $kyc)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center text-white small fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($kyc->user?->email ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white fw-medium">{{ $kyc->user?->email }}</div>
                                        <div class="small" style="color: rgba(226, 232, 240, 0.7); font-size: 0.75rem;">ID: #{{ Str::limit($kyc->id, 8, '') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($kyc->first_name)
                                    <div class="text-white">{{ $kyc->first_name }} {{ $kyc->last_name }}</div>
                                @else
                                    <span class="small" style="color: rgba(226, 232, 240, 0.6); font-style: italic;">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColor = match($kyc->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        'draft' => 'secondary',
                                        default => 'secondary'
                                    };
                                    $statusIcon = match($kyc->status) {
                                        'approved' => 'bi-check-circle-fill',
                                        'pending' => 'bi-clock-fill',
                                        'rejected' => 'bi-x-circle-fill',
                                        'draft' => 'bi-pencil-fill',
                                        default => 'bi-circle'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} border-opacity-25 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    {{ ucfirst($kyc->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-white small">{{ $kyc->updated_at?->format('M d, Y') }}</div>
                                <div class="small" style="color: rgba(226, 232, 240, 0.6); font-size: 0.75rem;">{{ $kyc->updated_at?->format('h:i A') }}</div>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('admin.kyc.show', $kyc) }}" class="btn btn-sm btn-outline-light border-opacity-25 hover-lift">
                                    Review <i class="bi bi-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-person-vcard fs-1 text-muted opacity-25 mb-3"></i>
                                    <div class="text-muted">No KYC records found</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-3">
            {{ $kycs->links() }}
        </div>
    </div>
@endsection

