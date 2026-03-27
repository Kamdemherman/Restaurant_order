{{-- FILE: resources/views/auth/register.blade.php --}}
@extends('layouts.app')
@section('title','Register')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%)">
    <div class="card p-4 p-md-5" style="width:100%;max-width:500px">
        <div class="text-center mb-4">
            <div style="font-size:2.5rem">🍽</div>
            <h4 class="fw-bold mt-2" style="font-family:'Playfair Display',serif">Create Account</h4>
            <p class="text-muted small">Registration requires admin approval</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Phone Number</label>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" placeholder="+353 ...">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter" value="1">
                <label class="form-check-label small" for="newsletter">
                    Subscribe to our newsletter for offers and updates
                </label>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input @error('gdpr_consent') is-invalid @enderror"
                       type="checkbox" name="gdpr_consent" id="gdpr" value="1" required>
                <label class="form-check-label small" for="gdpr">
                    I agree to the <a href="#" target="_blank">Privacy Policy</a> and consent to
                    the processing of my personal data. <span class="text-danger">*</span>
                </label>
                @error('gdpr_consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">Create Account</button>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted small mb-0">
            Already have an account? <a href="{{ route('login') }}" class="fw-medium" style="color:#e94560">Sign in</a>
        </p>
    </div>
</div>
@endsection
