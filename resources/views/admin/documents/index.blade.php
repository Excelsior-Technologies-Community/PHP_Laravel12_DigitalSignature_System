@extends('layouts.app')

@section('title', 'All Documents')

@section('content')
<div class="card auth-card p-4">
    <h4 class="mb-3">All Documents</h4>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>File</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Signed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>{{ $doc->file_name }}</td>
                        <td>{{ $doc->user->name }}</td>
                        <td>
                            <span class="badge bg-{{ $doc->status === 'signed' ? 'success' : 'warning' }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>{{ $doc->signed_at ? $doc->signed_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No documents found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $documents->links() }}
    </div>
</div>
@endsection
