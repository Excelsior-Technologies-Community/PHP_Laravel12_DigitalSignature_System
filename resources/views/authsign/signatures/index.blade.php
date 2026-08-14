@extends('layouts.app')

@section('title', 'My Signatures')

@section('content')
<div class="card auth-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Signatures</h4>
        <a href="{{ route('signature.form') }}" class="btn btn-primary btn-sm">+ Add New Signature</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($signatures as $sig)
                    <tr>
                        <td>
                            <img src="{{ asset('signatures/'.$sig->image_name) }}" width="100" height="50" style="object-fit: contain;">
                        </td>
                        <td>{{ $sig->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('signatures.download', $sig->id) }}" class="btn btn-sm btn-outline-primary">Download</a>
                            <form action="{{ route('signatures.destroy', $sig->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this signature?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No signatures found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
