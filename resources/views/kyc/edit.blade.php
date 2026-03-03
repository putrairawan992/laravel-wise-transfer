@extends('layouts.app')

@section('header_title', 'e-KYC Verification')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @php
            $readOnly = in_array($kyc->status, ['pending', 'approved'], true);
            $isRejected = $kyc->status === 'rejected';
        @endphp

        <!-- Header Status -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Identity Verification</h4>
                        <div class="text-muted small">Complete the form below to verify your identity.</div>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill px-3 py-2 fs-6 
                            {{ $kyc->status === 'approved' ? 'bg-success' : 
                               ($kyc->status === 'pending' ? 'bg-warning text-dark' : 
                               ($kyc->status === 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">
                            {{ ucfirst($kyc->status) }}
                        </span>
                        @if($kyc->status === 'pending')
                             <div class="small mt-1 text-muted">Submitted: {{ $kyc->submitted_at?->format('M d, Y') }}</div>
                        @endif
                    </div>
                </div>
                
                @if($isRejected && $kyc->rejection_reason)
                    <div class="alert alert-danger mt-3 mb-0 border-0 bg-danger-subtle text-danger-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Verification Rejected:</strong> {{ $kyc->rejection_reason }}
                    </div>
                @endif
            </div>
            <div class="bg-light px-4 py-2 small text-muted border-top d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-primary"></i> Your data is encrypted and stored securely (AES-256).
            </div>
        </div>

        <form action="{{ route('kyc.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">First Name</label>
                            <input type="text" name="first_name" class="form-control form-control-lg" value="{{ old('first_name', $kyc->first_name) }}" placeholder="e.g. John" required @disabled($readOnly)>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Last Name</label>
                            <input type="text" name="last_name" class="form-control form-control-lg" value="{{ old('last_name', $kyc->last_name) }}" placeholder="e.g. Doe" required @disabled($readOnly)>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Address</label>
                            <input type="text" name="address" class="form-control form-control-lg" value="{{ old('address', $kyc->address_enc) }}" placeholder="Full residential address" required @disabled($readOnly)>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control form-control-lg" value="{{ old('contact_number', $kyc->contact_number) }}" placeholder="+1 234 567 890" required @disabled($readOnly)>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-text-fill me-2"></i>Documents</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4">
                        <!-- Identity Document -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Identity Document Number (IC / KTP / Passport)</label>
                            <input type="text" name="document_number" class="form-control form-control-lg mb-2" value="{{ old('document_number', $kyc->document_number_enc) }}" placeholder="Enter document ID number" required @disabled($readOnly)>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label fw-bold mb-2">Identity Document</label>
                                <div class="small text-muted mb-3">Upload clear photo of ID/Passport</div>
                                <input type="file" name="document_file" class="form-control mb-3" accept=".png,.jpg,.jpeg,.pdf" onchange="previewImage(this, 'preview_doc')" @disabled($readOnly)>
                                <div class="ratio ratio-16x9 bg-white border rounded overflow-hidden d-flex align-items-center justify-content-center">
                                    @if($kyc->document_file_path)
                                        <img id="preview_doc" src="{{ route('kyc.view', 'document') }}" class="w-100 h-100 object-fit-cover" alt="Preview">
                                    @else
                                        <img id="preview_doc" src="" class="w-100 h-100 object-fit-cover d-none" alt="Preview">
                                        <div id="placeholder_doc" class="d-flex align-items-center justify-content-center w-100 h-100 text-muted small">No image</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label fw-bold mb-2">Bank Account</label>
                                <div class="small text-muted mb-3">First page of bank book/statement</div>
                                <input type="file" name="bank_account_file" class="form-control mb-3" accept=".png,.jpg,.jpeg,.pdf" onchange="previewImage(this, 'preview_bank')" @disabled($readOnly)>
                                <div class="ratio ratio-16x9 bg-white border rounded overflow-hidden">
                                    @if($kyc->bank_account_file_path)
                                        <img id="preview_bank" src="{{ route('kyc.view', 'bank') }}" class="w-100 h-100 object-fit-cover" alt="Preview">
                                    @else
                                        <img id="preview_bank" src="" class="w-100 h-100 object-fit-cover d-none" alt="Preview">
                                        <div id="placeholder_bank" class="d-flex align-items-center justify-content-center w-100 h-100 text-muted small">No image</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="form-label fw-bold mb-2">Utility Bill</label>
                                <div class="small text-muted mb-3">Proof of address (water/electric bill)</div>
                                <input type="file" name="utility_bill_file" class="form-control mb-3" accept=".png,.jpg,.jpeg,.pdf" onchange="previewImage(this, 'preview_utility')" @disabled($readOnly)>
                                <div class="ratio ratio-16x9 bg-white border rounded overflow-hidden">
                                    @if($kyc->utility_bill_file_path)
                                        <img id="preview_utility" src="{{ route('kyc.view', 'utility') }}" class="w-100 h-100 object-fit-cover" alt="Preview">
                                    @else
                                        <img id="preview_utility" src="" class="w-100 h-100 object-fit-cover d-none" alt="Preview">
                                        <div id="placeholder_utility" class="d-flex align-items-center justify-content-center w-100 h-100 text-muted small">No image</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Face Recognition -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-bounding-box me-2"></i>Facial Recognition</h5>
                    <div class="badge bg-primary-subtle text-primary border border-primary-subtle">Required</div>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="text-muted small mb-4">Please upload photos of your face from different angles as requested below. Ensure good lighting and no accessories blocking your face.</p>
                    
                    <div class="row g-3 justify-content-center">
                        @foreach(['straight' => 'Straight', 'left' => 'Left Side', 'right' => 'Right Side', 'top' => 'Top Angle', 'bottom' => 'Bottom Angle'] as $key => $label)
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="text-center">
                                <div class="ratio ratio-1x1 bg-light border rounded mb-2 position-relative overflow-hidden group-hover-overlay">
                                    @if($kyc->{'face_'.$key.'_path'})
                                        <img id="preview_face_{{$key}}" src="{{ route('kyc.view', 'face_'.$key) }}" class="w-100 h-100 object-fit-cover" alt="{{ $label }}">
                                    @else
                                        <img id="preview_face_{{$key}}" src="" class="w-100 h-100 object-fit-cover d-none" alt="{{ $label }}">
                                        <div id="placeholder_face_{{$key}}" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                            <i class="bi bi-person fs-1 opacity-25"></i>
                                        </div>
                                    @endif
                                    
                                    @if(!$readOnly)
                                    <div class="position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-50 text-center">
                                        <label class="btn btn-sm btn-light py-0 px-2 small w-100 stretched-link" style="font-size: 10px;">
                                            Upload
                                            <input type="file" name="face_{{$key}}" class="d-none" accept=".png,.jpg,.jpeg" onchange="previewImage(this, 'preview_face_{{$key}}')">
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                <div class="fw-semibold small">{{ $label }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-3 mb-5">
                @if(!$readOnly)
                    <button type="submit" class="btn btn-md btn-light border px-4 shadow-sm">Save Draft</button>
                @endif
                
                @if(!$readOnly && in_array($kyc->status, ['draft', 'rejected'], true))
                    <button type="submit" formaction="{{ route('kyc.submit') }}" class="btn btn-md btn-primary px-4 shadow fw-bold">
                        Submit Verification <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                const placeholder = document.getElementById(previewId.replace('preview', 'placeholder'));
                
                img.src = e.target.result;
                img.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .object-fit-cover { object-fit: cover; }
    .form-control-lg { font-size: 0.95rem; }
    .card { transition: transform 0.2s; }
    /* .card:hover { transform: translateY(-2px); } */
</style>
@endsection
