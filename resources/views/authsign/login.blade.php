@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">

            <h4 class="mb-3 text-center">Login</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.check') }}">
                @csrf

                <input class="form-control mb-3" name="email" placeholder="Email" value="{{ old('email') }}">

                <input type="password" class="form-control mb-3" name="password" placeholder="Password">

                <button class="btn btn-success w-100">
                    Login
                </button>
            </form>
<p class="text-center mt-3">
    Don’t have an account?
    <a href="{{ route('register.form') }}">Create Account</a>
</p>

        </div>
    </div>
</div>
@endsection
