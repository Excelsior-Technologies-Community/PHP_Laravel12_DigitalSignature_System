@extends('layouts.app')

@section('title', 'My Documents')

@section('content')
<div class="card auth-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Documents</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">+ Upload Document</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>{{ $doc->file_name }}</td>
                        <td>{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                        <td>
                            <span class="badge bg-{{ $doc->status === 'signed' ? 'success' : 'warning' }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('documents.verify', $doc->id) }}" class="btn btn-sm btn-outline-info">Verify</a>
                            <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-sm btn-outline-primary">Download</a>
                            @if($doc->status === 'pending')
                                <a href="{{ route('documents.sign', $doc->id) }}" class="btn btn-sm btn-success">Sign</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No documents found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="file" name="document" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
