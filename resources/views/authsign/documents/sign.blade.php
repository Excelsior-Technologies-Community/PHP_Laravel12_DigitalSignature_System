@extends('layouts.app')

@section('title', 'Sign Document')

@section('content')
<div class="card auth-card p-4">
    <h4 class="mb-3">Sign Document</h4>
    <p><strong>File:</strong> {{ $document->file_name }}</p>
    <p><strong>Status:</strong> {{ ucfirst($document->status) }}</p>

    <div class="mb-3">
        <img src="{{ asset('signatures/'.$user->signature) }}" width="150">
    </div>

    <form method="POST" action="{{ route('documents.sign.save', $document->id) }}">
        @csrf
        <button class="btn btn-success">Sign This Document</button>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
