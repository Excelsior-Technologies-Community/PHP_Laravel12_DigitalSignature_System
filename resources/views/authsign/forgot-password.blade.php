@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">
            <h4 class="mb-3 text-center">Forgot Password</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <p class="text-muted">Enter your email to reset your password.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input class="form-control mb-3" name="email" placeholder="Email" value="{{ old('email') }}">

                <button class="btn btn-primary w-100">Send Reset Link</button>
            </form>
            <p class="text-center mt-3">
                <a href="{{ route('login.form') }}">Back to Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
