@extends('layouts.admin')

@section('title', 'Admin - View Ticket')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary rounded-pill px-4 border-secondary border-opacity-25 text-light hover-bg-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.enquiries.updateStatus', $enquiry) }}" method="POST" class="d-flex align-items-center gap-2 bg-dark-card p-1 rounded-pill border border-secondary border-opacity-25">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select form-select-sm border-0 bg-transparent text-light focus-none py-1 ps-3 pe-4" onchange="this.form.submit()" style="cursor: pointer;">
                            <option value="open" {{ $enquiry->status === 'open' ? 'selected' : '' }} class="bg-dark text-light">Open</option>
                            <option value="pending" {{ $enquiry->status === 'pending' ? 'selected' : '' }} class="bg-dark text-light">Pending</option>
                            <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }} class="bg-dark text-light">Closed</option>
                        </select>
                        <span class="badge rounded-pill px-3 py-2 {{ $enquiry->status === 'open' ? 'bg-success' : ($enquiry->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($enquiry->status) }}
                        </span>
                    </form>
                </div>
            </div>

            <div class="card shadow-lg border-0 bg-dark-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-secondary border-opacity-10 p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 1.5rem; font-weight: bold; background: linear-gradient(135deg, #6366f1, #a855f7);">
                                {{ substr($enquiry->user->name ?? 'G', 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bold text-white mb-1">{{ $enquiry->subject }}</h4>
                            <div class="d-flex align-items-center text-secondary small gap-3">
                                <span><i class="bi bi-person me-1"></i> {{ $enquiry->user->name ?? 'Guest User' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-envelope me-1"></i> {{ $enquiry->user->email ?? 'No Email' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-clock me-1"></i> {{ $enquiry->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                        <div class="text-secondary opacity-50 display-6">
                            <i class="bi bi-quote"></i>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4 bg-dark bg-opacity-50">
                    <div class="message-content text-light lh-lg" style="white-space: pre-wrap;">{{ $enquiry->message }}</div>
                </div>

                <div class="card-footer bg-transparent border-top border-secondary border-opacity-10 p-4">
                    <h6 class="text-secondary text-uppercase small fw-bold mb-3">Quick Actions</h6>
                    <div class="d-flex gap-2">
                        <a href="mailto:{{ $enquiry->user->email }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-reply-fill me-2"></i> Reply via Email
                        </a>
                        @if($enquiry->user && $enquiry->user->mobile)
                            <a href="https://wa.me/{{ $enquiry->user->mobile }}" target="_blank" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-whatsapp me-2"></i> Chat on WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-card { background-color: #1e293b; }
    .focus-none:focus { box-shadow: none; }
    .hover-bg-secondary:hover { background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); }
    .message-content { font-size: 1.05rem; color: #e2e8f0; }
</style>
@endsection
