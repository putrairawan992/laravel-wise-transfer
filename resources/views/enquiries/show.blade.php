@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $enquiry->subject }}</span>
                    <span class="badge bg-{{ $enquiry->status === 'open' ? 'success' : 'secondary' }}">
                        {{ ucfirst($enquiry->status) }}
                    </span>
                </div>

                <div class="card-body">
                    <p>{{ $enquiry->message }}</p>
                    <hr>
                    <p class="text-muted small">Submitted on {{ $enquiry->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
