@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Enquiries</h2>
        <a href="{{ route('enquiries.create') }}" class="btn btn-primary">New Enquiry</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($enquiries->isEmpty())
                <p class="text-muted">No enquiries found.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enquiries as $enquiry)
                            <tr>
                                <td>{{ $enquiry->subject }}</td>
                                <td>
                                    <span class="badge bg-{{ $enquiry->status === 'open' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($enquiry->status) }}
                                    </span>
                                </td>
                                <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
