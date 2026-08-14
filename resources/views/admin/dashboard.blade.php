@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card auth-card p-3">
            <h5>Total Users</h5>
            <h2>{{ $totalUsers }}</h2>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card auth-card p-3">
            <h5>Total Documents</h5>
            <h2>{{ $totalDocs }}</h2>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card auth-card p-3">
            <h5>Signed</h5>
            <h2>{{ $signedDocs }}</h2>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card auth-card p-3">
            <h5>Pending</h5>
            <h2>{{ $pendingDocs }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card auth-card p-3">
            <h5>Recent Users</h5>
            <table class="table table-sm">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No users</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card auth-card p-3">
            <h5>Recent Activity</h5>
            <table class="table table-sm">
                <thead>
                    <tr><th>Action</th><th>User</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                            <td>{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No logs</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
