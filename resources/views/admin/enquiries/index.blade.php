@extends('layouts.admin')

@section('title', 'Admin - Support Tickets')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-4 text-white fw-bold">Support Tickets</h2>
            <div class="card shadow-sm border-0 bg-dark-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                            <thead style="background-color: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <tr>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold" style="width: 80px;">ID</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold" style="width: 250px;">User</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Subject</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold" style="width: 120px;">Status</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold" style="width: 180px;">Date</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold text-end" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enquiries as $enquiry)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-secondary">#{{ $enquiry->id }}</td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary bg-opacity-25 text-primary d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; font-weight: bold;">
                                                    {{ substr($enquiry->user->name ?? 'G', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-white">{{ $enquiry->user->name ?? 'Guest' }}</div>
                                                    <div class="small text-secondary" style="font-size: 0.75rem;">{{ $enquiry->user->email ?? 'No Email' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="d-block text-white fw-medium mb-1">{{ Str::limit($enquiry->subject, 50) }}</span>
                                            <span class="small text-secondary">{{ Str::limit($enquiry->message, 60) }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($enquiry->status === 'open')
                                                <span class="badge bg-success text-white rounded-pill px-3 py-2">Open</span>
                                            @elseif($enquiry->status === 'pending')
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                            @else
                                                <span class="badge bg-secondary text-white rounded-pill px-3 py-2">Closed</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-secondary small">
                                            <i class="bi bi-calendar3 me-1"></i> {{ $enquiry->created_at->format('d M Y') }}<br>
                                            <i class="bi bi-clock me-1"></i> {{ $enquiry->created_at->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 hover-scale">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-secondary">
                                            <div class="mb-3">
                                                <i class="bi bi-inbox fs-1 text-muted opacity-25"></i>
                                            </div>
                                            No support tickets found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-secondary border-opacity-10 py-3">
                    {{ $enquiries->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-card { background-color: #1e293b; }
    .table-dark { --bs-table-bg: transparent; color: #f1f5f9; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }
</style>
@endsection
