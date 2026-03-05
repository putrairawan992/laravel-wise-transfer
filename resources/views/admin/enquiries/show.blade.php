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
                    <div class="message-content text-light lh-lg mb-5" style="white-space: pre-wrap;">{{ $enquiry->message }}</div>

                    <!-- Replies Section -->
                    @if($enquiry->replies->count() > 0)
                        <h6 class="text-secondary text-uppercase small fw-bold mb-4">Conversation History</h6>
                        <div class="replies-container d-flex flex-column gap-3 mb-4">
                            @foreach($enquiry->replies as $reply)
                                <div class="d-flex {{ $reply->user_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="card border-0 {{ $reply->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-dark-card border border-secondary border-opacity-25' }}" style="max-width: 80%; border-radius: 1rem;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                                                <small class="fw-bold {{ $reply->user_id === auth()->id() ? 'text-white-50' : 'text-primary' }}">
                                                    {{ $reply->user->name }} ({{ $reply->user->role }})
                                                </small>
                                                <small class="{{ $reply->user_id === auth()->id() ? 'text-white-50' : 'text-secondary' }}" style="font-size: 0.7rem;">
                                                    {{ $reply->created_at->format('d M, H:i') }}
                                                </small>
                                            </div>
                                            <p class="mb-0 {{ $reply->user_id === auth()->id() ? 'text-white' : 'text-light' }}" style="white-space: pre-wrap;">{{ $reply->message }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-transparent border-top border-secondary border-opacity-10 p-4">
                    <h6 class="text-secondary text-uppercase small fw-bold mb-3">Reply to User</h6>
                    
                    <form action="{{ route('admin.enquiries.reply', $enquiry) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-dark text-light border-secondary border-opacity-25 focus-none" rows="3" placeholder="Type your reply here..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-secondary small">
                                <i class="bi bi-info-circle me-1"></i> User will see this reply in their dashboard.
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Send Reply
                            </button>
                        </div>
                    </form>

                    <hr class="border-secondary border-opacity-10 my-4">

                    <h6 class="text-secondary text-uppercase small fw-bold mb-3">External Actions</h6>
                    <div class="d-flex gap-2">
                        <a href="mailto:{{ $enquiry->user->email }}" class="btn btn-outline-secondary rounded-pill px-4 text-light border-opacity-25 hover-bg-secondary">
                            <i class="bi bi-envelope me-2"></i> Email
                        </a>
                        @if($enquiry->user && $enquiry->user->mobile)
                            <a href="https://wa.me/{{ $enquiry->user->mobile }}" target="_blank" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-whatsapp me-2"></i> WhatsApp
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
