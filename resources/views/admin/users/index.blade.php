@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="card auth-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Users</h4>
        <form method="GET" action="{{ route('admin.users.search') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm">Search</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <form action="{{ route('admin.users.toggleAdmin', $user->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-{{ $user->is_admin ? 'warning' : 'success' }}">
                                    {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
