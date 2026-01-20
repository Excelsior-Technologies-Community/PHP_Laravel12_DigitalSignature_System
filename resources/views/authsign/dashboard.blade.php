@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card auth-card p-4">

    <h4 class="mb-3">Dashboard</h4>

    <p class="text-muted">Your current digital signature:</p>

    <img src="{{ asset('signatures/'.$user->signature) }}"
         class="mb-3"
         style="max-width:300px; border:1px solid #ccc; padding:10px; background:#fff;">

    <br>

    <a href="{{ route('signature.form') }}" class="btn btn-warning">
        Update Signature
    </a>

</div>
@endsection
