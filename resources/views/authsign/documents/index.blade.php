@extends('layouts.app')

@section('title', 'My Documents')

@section('content')

<div class="card auth-card p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>
            <i class="bi bi-file-earmark-text me-2"></i>
            My Documents
        </h4>

        <div class="d-flex gap-2">

            <a
                href="{{ route('documents.verify.form') }}"
                class="btn btn-outline-success btn-sm"
            >
                <i class="bi bi-shield-check"></i>
                Verify Document
            </a>

            <button
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#uploadModal"
            >
                <i class="bi bi-upload"></i>
                Upload Document
            </button>

        </div>

    </div>


    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
        </div>
    @endif


    {{-- Error --}}
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ session('error') }}
        </div>
    @endif


    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Expiry</th>
                    <th>Verification ID</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                @forelse($documents as $doc)

                    <tr>

                        {{-- File --}}
                        <td>

                            <i class="bi bi-file-earmark me-1"></i>

                            {{ $doc->file_name }}

                        </td>


                        {{-- Size --}}
                        <td>
                            {{ number_format($doc->file_size / 1024, 2) }} KB
                        </td>


                        {{-- Status --}}
                        <td>

                            @if($doc->status === 'signed')

                                @if($doc->isExpired())

                                    <span class="badge bg-danger">
                                        Expired
                                    </span>

                                @else

                                    <span class="badge bg-success">
                                        Signed
                                    </span>

                                @endif

                            @else

                                @if($doc->isExpired())

                                    <span class="badge bg-danger">
                                        Expired
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @endif

                            @endif

                        </td>


                        {{-- Expiry --}}
                        <td>

                            @if($doc->expires_at)

                                @if($doc->isExpired())

                                    <span class="text-danger fw-semibold">
                                        {{ $doc->expires_at->format('d M Y') }}
                                        <br>
                                        <small>Expired</small>
                                    </span>

                                @else

                                    <span>
                                        {{ $doc->expires_at->format('d M Y') }}
                                    </span>

                                @endif

                            @else

                                <span class="text-muted">
                                    No expiry
                                </span>

                            @endif

                        </td>


                        {{-- Verification ID --}}
                        <td>

                            @if($doc->verification_code)

                                <code>
                                    {{ $doc->verification_code }}
                                </code>

                            @else

                                <span class="text-muted">
                                    Not generated
                                </span>

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td>

                            <div class="d-flex flex-wrap gap-1">

                                <a
                                    href="{{ route('documents.verify', $doc->id) }}"
                                    class="btn btn-sm btn-outline-info"
                                >
                                    Verify
                                </a>


                                <a
                                    href="{{ route('documents.download', $doc->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Download
                                </a>


                                @if($doc->status === 'pending' && !$doc->isExpired())

                                    <a
                                        href="{{ route('documents.sign', $doc->id) }}"
                                        class="btn btn-sm btn-success"
                                    >
                                        Sign
                                    </a>

                                @elseif($doc->status === 'pending' && $doc->isExpired())

                                    <button
                                        class="btn btn-sm btn-secondary"
                                        disabled
                                    >
                                        Expired
                                    </button>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            <i class="bi bi-file-earmark-x fs-3"></i>

                            <div class="mt-2">
                                No documents found.
                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- Upload Modal --}}
<div
    class="modal fade"
    id="uploadModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>
                    Upload Document
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <form
                action="{{ route('documents.upload') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <div class="modal-body">

                    {{-- Document --}}
                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Select Document
                        </label>

                        <input
                            type="file"
                            name="document"
                            class="form-control"
                            required
                        >

                        <small class="text-muted">
                            Maximum file size: 10 MB
                        </small>

                    </div>


                    {{-- Expiry --}}
                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Document Expiry Date
                        </label>

                        <input
                            type="date"
                            name="expires_at"
                            class="form-control"
                            min="{{ date('Y-m-d') }}"
                        >

                        <small class="text-muted">
                            Leave empty if the document does not expire.
                        </small>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-upload me-1"></i>
                        Upload Document
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection