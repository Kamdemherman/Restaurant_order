{{-- FILE: resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%)">
    <div class="card p-4 p-md-5" style="width:100%;max-width:420px">
        <div class="text-center mb-4">
            <div style="font-size:2.5rem">🍽</div>
            <h4 class="fw-bold mt-2" style="font-family:'Playfair Display',serif">{{ config('app.name') }}</h4>
            <p class="text-muted small">Sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Email address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">Sign In</button>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted small mb-0">
            Don't have an account?
            <a href="{{ route('register') }}" class="fw-medium" style="color:var(--brand-accent,#e94560)">Register here</a>
        </p>
    </div>
</div>
@endsection
