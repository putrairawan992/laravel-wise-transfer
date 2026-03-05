@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> Back to My Enquiries
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">{{ $enquiry->subject }}</h5>
                        <span class="badge {{ $enquiry->status === 'open' ? 'bg-success' : ($enquiry->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }} rounded-pill px-3 py-2">
                            {{ ucfirst($enquiry->status) }}
                        </span>
                    </div>
                    <small class="text-muted">Ticket ID: #{{ $enquiry->id }} &bull; {{ $enquiry->created_at->format('d M Y, H:i') }}</small>
                </div>

                <div class="card-body p-4 bg-light">
                    <!-- Original Message -->
                    <div class="d-flex justify-content-end mb-4">
                        <div class="card border-0 bg-primary text-white" style="max-width: 80%; border-radius: 1rem 1rem 0 1rem;">
                            <div class="card-body p-3">
                                <p class="mb-0">{{ $enquiry->message }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Replies -->
                    @foreach($enquiry->replies as $reply)
                        <div class="d-flex {{ $reply->user_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-4">
                            @if($reply->user_id !== auth()->id())
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        A
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card border-0 {{ $reply->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-white shadow-sm' }}" style="max-width: 80%; border-radius: {{ $reply->user_id === auth()->id() ? '1rem 1rem 0 1rem' : '1rem 1rem 1rem 0' }};">
                                <div class="card-body p-3">
                                    @if($reply->user_id !== auth()->id())
                                        <div class="mb-1">
                                            <small class="fw-bold text-dark">Support Agent</small>
                                        </div>
                                    @endif
                                    <p class="mb-0 {{ $reply->user_id === auth()->id() ? '' : 'text-dark' }}">{{ $reply->message }}</p>
                                    <div class="text-end mt-1">
                                        <small class="{{ $reply->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.7rem;">
                                            {{ $reply->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer bg-white border-top p-4">
                    <form action="{{ route('enquiries.reply', $enquiry) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Add a Reply</label>
                            <textarea name="message" class="form-control rounded-3" rows="3" placeholder="Type your message here..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
