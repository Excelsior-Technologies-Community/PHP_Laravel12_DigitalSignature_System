@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>User Info</h5>
            <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="mb-1"><strong>Registered:</strong> {{ $user->created_at->format('d M Y') }}</p>
            <p class="mb-0"><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'N/A' }}</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>Current Signature</h5>
            @if($user->signature)
                <img src="{{ asset('signatures/'.$user->signature) }}" width="100%" class="img-fluid">
            @else
                <p class="text-muted">No signature</p>
            @endif
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>Quick Actions</h5>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm mb-2">Edit Profile</a>
            <a href="{{ route('profile.password') }}" class="btn btn-warning btn-sm mb-2">Change Password</a>
            <a href="{{ route('signatures.all') }}" class="btn btn-success btn-sm mb-2">My Signatures</a>
            <a href="{{ route('documents.index') }}" class="btn btn-info btn-sm">Documents</a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>Recent Signatures</h5>
            @forelse($signatures as $sig)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span>{{ $sig->created_at->format('d M Y') }}</span>
                    <a href="{{ route('signatures.download', $sig->id) }}" class="btn btn-sm btn-outline-primary">Download</a>
                </div>
            @empty
                <p class="text-muted">No signatures yet</p>
            @endforelse
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>Recent Documents</h5>
            @forelse($documents as $doc)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span class="text-truncate" style="max-width: 150px;">{{ $doc->file_name }}</span>
                    <span class="badge bg-{{ $doc->status === 'signed' ? 'success' : 'warning' }}">
                        {{ ucfirst($doc->status) }}
                    </span>
                </div>
            @empty
                <p class="text-muted">No documents yet</p>
            @endforelse
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card auth-card p-3">
            <h5>Recent Activity</h5>
            @forelse($recentLogs as $log)
                <div class="border-bottom py-2">
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    <p class="mb-0">{{ $log->action }}</p>
                </div>
            @empty
                <p class="text-muted">No activity</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
