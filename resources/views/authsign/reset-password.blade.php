@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">
            <h4 class="mb-3 text-center">Reset Password</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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

            <form method="POST" action="{{ route('password.update', $token) }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input class="form-control mb-3" name="email" placeholder="Email" value="{{ old('email') }}">

                <input type="password" class="form-control mb-3" name="password" placeholder="New Password">

                <input type="password" class="form-control mb-3" name="password_confirmation" placeholder="Confirm Password">

                <button class="btn btn-primary w-100">Reset Password</button>
            </form>
            <p class="text-center mt-3">
                <a href="{{ route('login.form') }}">Back to Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
