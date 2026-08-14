@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card auth-card p-4">
            <h4 class="mb-3">Change Password</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                <input type="password" class="form-control mb-3" name="current_password" placeholder="Current Password">

                <input type="password" class="form-control mb-3" name="password" placeholder="New Password">

                <input type="password" class="form-control mb-3" name="password_confirmation" placeholder="Confirm New Password">

                <button class="btn btn-warning w-100">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
