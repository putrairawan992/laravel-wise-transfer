@extends('layouts.guest')

@section('content')
<div class="row justify-content-center w-100">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-wallet2 fs-2 text-primary"></i>
            </div>
            <h3 class="fw-bold text-dark">Welcome Back</h3>
            <p class="text-muted">Sign in to your secure dashboard</p>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-secondary small text-uppercase">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-light border-start-0 ps-0 py-2" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold text-secondary small text-uppercase mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control bg-light border-start-0 ps-0 py-2" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold py-2">
                            Sign in <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            &copy; {{ date('Y') }} WisePay. Secure Payment System.
        </div>
    </div>
</div>

<style>
    .form-control:focus, .input-group-text { border-color: #e2e8f0; box-shadow: none; }
    .form-control:focus { background-color: #fff; }
    .input-group:focus-within .input-group-text { background-color: #fff; border-color: #4f46e5; }
    .input-group:focus-within .form-control { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    .btn-primary { background-color: #4f46e5; border-color: #4f46e5; }
    .btn-primary:hover { background-color: #4338ca; border-color: #4338ca; }
    .text-primary { color: #4f46e5 !important; }
</style>
@endsection
