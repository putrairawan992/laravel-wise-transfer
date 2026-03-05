@extends('layouts.admin')

@section('title', 'Admin - WhatsApp Manager')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-4 text-white fw-bold">WhatsApp Manager</h2>
            
            <!-- Broadcast Form -->
            <div class="card shadow-sm border-0 bg-dark-card mb-4">
                <div class="card-header bg-transparent border-bottom border-secondary border-opacity-10 py-3">
                    <h5 class="fw-bold text-white mb-0"><i class="bi bi-broadcast me-2"></i> Send Broadcast Message</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.whatsapp.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Recipient</label>
                            <select name="user_id" class="form-select bg-dark text-light border-secondary border-opacity-25 focus-none">
                                <option value="all">All Users (Broadcast)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->mobile ?? 'No Number' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Message</label>
                            <textarea name="message" rows="4" class="form-control bg-dark text-light border-secondary border-opacity-25 focus-none" placeholder="Type your message here..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Message Logs -->
            <h5 class="text-white fw-bold mb-3">Message Logs</h5>
            <div class="card shadow-sm border-0 bg-dark-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                            <thead style="background-color: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <tr>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Date</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Recipient</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Message</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Status</th>
                                    <th class="px-4 py-3 text-secondary text-uppercase small font-weight-bold">Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-secondary small">
                                            {{ $log->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold text-white">{{ $log->user->name ?? 'Unknown User' }}</div>
                                            <div class="small text-secondary">{{ $log->recipient_number }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-light">
                                            {{ Str::limit($log->message, 50) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->status === 'sent')
                                                <span class="badge bg-success text-white rounded-pill px-3 py-2">Sent</span>
                                            @else
                                                <span class="badge bg-danger text-white rounded-pill px-3 py-2" title="{{ $log->error_message }}">Failed</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-secondary small">
                                            {{ $log->admin->name ?? 'System' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">No messages sent yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-secondary border-opacity-10 py-3">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-card { background-color: #1e293b; }
    .table-dark { --bs-table-bg: transparent; color: #f1f5f9; }
    .focus-none:focus { box-shadow: none; border-color: var(--bs-primary); }
</style>
@endsection
