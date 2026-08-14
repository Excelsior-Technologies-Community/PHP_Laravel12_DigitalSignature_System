@extends('layouts.app')

@section('title', 'Verify Document')

@section('content')
<div class="card auth-card p-4">
    <h4 class="mb-3">Document Verification</h4>
    <p><strong>File:</strong> {{ $document->file_name }}</p>
    <p><strong>User:</strong> {{ $document->user->name ?? 'N/A' }}</p>
    <p><strong>Status:</strong> {{ $signed ? 'Signed' : 'Pending' }}</p>

    @if($signed && $document->user)
        <div class="alert alert-success">
            <strong>Signed At:</strong> {{ $document->signed_at->format('d M Y H:i') }}
        </div>
        @if($document->user->signature)
            <img src="{{ asset('signatures/'.$document->user->signature) }}" width="150" class="img-fluid">
        @endif
    @else
        <div class="alert alert-warning">This document is not yet signed</div>
    @endif

    <a href="{{ route('documents.index') }}" class="btn btn-secondary mt-3">Back to Documents</a>
</div>
@endsection
