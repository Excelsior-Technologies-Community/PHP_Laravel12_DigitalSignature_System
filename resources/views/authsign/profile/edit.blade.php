@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card auth-card p-4">
            <h4 class="mb-3">Edit Profile</h4>

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

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                <input class="form-control mb-3" name="name" value="{{ old('name', $user->name) }}" placeholder="Full Name">

                <input class="form-control mb-3" name="email" value="{{ old('email', $user->email) }}" placeholder="Email">

                <button class="btn btn-primary w-100">Update Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection
