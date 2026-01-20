@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card auth-card p-4">

    <h4>Dashboard</h4>

    <p class="text-muted mb-2">
        Your digital signature is saved successfully.
    </p>

    <img src="{{ asset('signatures/'.$user->signature) }}" width="250">

</div>
@endsection
