@extends('layouts.admin')

@section('title', 'e-KYC Details')
@section('page_title', 'e-KYC Details')
@section('page_subtitle', $kyc->user?->email)

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-white mb-0">Applicant Information</h5>
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
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Full Name</label>
                        <div class="text-white fw-medium fs-5">{{ trim(($kyc->first_name ?? '') . ' ' . ($kyc->last_name ?? '')) ?: 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Contact Number</label>
                        <div class="text-white">{{ $kyc->contact_number ?: 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Document Number</label>
                        <div class="text-white font-monospace bg-dark px-2 py-1 rounded d-inline-block border border-secondary border-opacity-25">
                            {{ $kyc->document_number_enc ?: 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Address</label>
                        <div class="text-white">{{ $kyc->address_enc ?: 'N/A' }}</div>
                    </div>
                </div>

                @if($kyc->status === 'rejected' && $kyc->rejection_reason)
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger mt-4 mb-0">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Rejection Reason</div>
                        <div>{{ $kyc->rejection_reason }}</div>
                    </div>
                @endif
            </div>

            <div class="card p-4 border-0 shadow-sm">
                <h5 class="fw-bold text-white mb-4">Uploaded Documents</h5>
                
                @php
                    $docs = [
                        ['key' => 'document', 'title' => 'Identity Document', 'desc' => 'National ID / Passport', 'path' => $kyc->document_file_path],
                        ['key' => 'bank', 'title' => 'Bank Account', 'desc' => 'First page of bank book', 'path' => $kyc->bank_account_file_path],
                        ['key' => 'utility', 'title' => 'Utility Bill', 'desc' => 'Proof of address', 'path' => $kyc->utility_bill_file_path],
                    ];
                    
                    $faces = [
                        ['key' => 'face_straight', 'title' => 'Straight', 'path' => $kyc->face_straight_path],
                        ['key' => 'face_left', 'title' => 'Left', 'path' => $kyc->face_left_path],
                        ['key' => 'face_right', 'title' => 'Right', 'path' => $kyc->face_right_path],
                        ['key' => 'face_top', 'title' => 'Top', 'path' => $kyc->face_top_path],
                        ['key' => 'face_bottom', 'title' => 'Bottom', 'path' => $kyc->face_bottom_path],
                    ];
                @endphp

                <div class="row g-3 mb-4">
                    @foreach($docs as $doc)
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border border-secondary border-opacity-25 h-100 position-relative group-hover-overlay" style="background: rgba(255,255,255,0.02);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                    @if($doc['path'])
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Uploaded</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Missing</span>
                                    @endif
                                </div>
                                <div class="fw-bold text-white mb-0">{{ $doc['title'] }}</div>
                                <div class="small text-white-50 mb-3">{{ $doc['desc'] }}</div>
                                
                                @if($doc['path'])
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-light border-opacity-10" onclick="showPreview('{{ route('admin.kyc.download', [$kyc, $doc['key']]) }}', '{{ $doc['title'] }}')">
                                            <i class="bi bi-eye me-1"></i> Preview
                                        </button>
                                        <a href="{{ route('admin.kyc.download', [$kyc, $doc['key']]) }}" class="btn btn-sm btn-outline-light border-opacity-10" download>
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <h6 class="fw-bold text-white mb-3">Face Verification</h6>
                <div class="row g-2">
                    @foreach($faces as $face)
                        <div class="col-6 col-md-2">
                            <div class="ratio ratio-1x1 rounded-3 overflow-hidden border border-secondary border-opacity-25 bg-dark position-relative group-hover-overlay">
                                @if($face['path'])
                                    <img src="{{ route('admin.kyc.download', [$kyc, $face['key']]) }}" class="w-100 h-100 object-fit-cover" alt="{{ $face['title'] }}">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 hover-opacity-100 transition-all gap-2">
                                        <button type="button" class="btn btn-sm btn-light rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="showPreview('{{ route('admin.kyc.download', [$kyc, $face['key']]) }}', '{{ $face['title'] }}')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <span class="small">N/A</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-center small text-muted mt-1">{{ $face['title'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-dark border border-secondary border-opacity-25">
                    <div class="modal-header border-bottom border-secondary border-opacity-25">
                        <h5 class="modal-title text-white" id="previewTitle">Document Preview</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 bg-black d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <img id="previewImage" src="" class="img-fluid" style="max-height: 80vh;" alt="Preview">
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showPreview(url, title) {
                document.getElementById('previewImage').src = url;
                document.getElementById('previewTitle').textContent = title;
                new bootstrap.Modal(document.getElementById('previewModal')).show();
            }
        </script>

        <div class="col-lg-4">
            <div class="card p-4 border-0 shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 1;">
                <h5 class="fw-bold text-white mb-3">Review Action</h5>
                <div class="d-grid gap-3">
                    <form action="{{ route('admin.kyc.approve', $kyc) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" onclick="return confirm('Are you sure you want to approve this KYC?')" {{ $kyc->status === 'approved' ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle-fill me-2"></i> Approve KYC
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal" {{ $kyc->status === 'rejected' ? 'disabled' : '' }}>
                        <i class="bi bi-x-circle-fill me-2"></i> Reject KYC
                    </button>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <div class="d-flex flex-column gap-3">
                    <div>
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Submission Date</label>
                        <div class="text-white">{{ $kyc->submitted_at?->format('M d, Y h:i A') ?? '—' }}</div>
                    </div>
                    <div>
                        <label class="small text-white-50 fw-bold text-uppercase mb-1">Last Updated</label>
                        <div class="text-white">{{ $kyc->updated_at?->format('M d, Y h:i A') }}</div>
                    </div>
                    @if($kyc->reviewer)
                        <div>
                            <label class="small text-white-50 fw-bold text-uppercase mb-1">Reviewed By</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-white small" style="width: 24px; height: 24px;">
                                    {{ substr($kyc->reviewer->email, 0, 1) }}
                                </div>
                                <div class="text-white small">{{ $kyc->reviewer->email }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #111827; border: 1px solid rgba(255,255,255,0.08); color: #e2e8f0;">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Reject e-KYC</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kyc.reject', $kyc) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label small" style="color: rgba(226,232,240,0.8);">Rejection reason</label>
                        <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" style="background: rgba(255,255,255,0.04); color: rgba(226,232,240,0.95); border: 1px solid rgba(255,255,255,0.08);" required></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.08); color: rgba(226,232,240,0.9);">Cancel</button>
                        <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.9); color: #0b1020;">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
