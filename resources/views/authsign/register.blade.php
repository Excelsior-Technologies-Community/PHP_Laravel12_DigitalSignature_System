@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card auth-card p-4">

            <h4 class="mb-3 text-center">Create Account</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.save') }}">
                @csrf

                <input class="form-control mb-3" name="name" placeholder="Full Name" value="{{ old('name') }}">

                <input class="form-control mb-3" name="email" placeholder="Email" value="{{ old('email') }}">

                <input type="password" class="form-control mb-3" name="password" placeholder="Password">

                <input type="password" class="form-control mb-3" name="password_confirmation" placeholder="Confirm Password">

                <button class="btn btn-primary w-100">
                    Register
                </button>
            </form>

            <p class="text-center mt-3 mb-0">
                Already have account?
                <a href="{{ route('login.form') }}">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
