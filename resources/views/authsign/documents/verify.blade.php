@extends('layouts.app')

@section('title', 'Verify Document')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-7">

        <div class="card auth-card p-4">

            <h4 class="mb-4">
                <i class="bi bi-shield-check me-2"></i>
                Document Verification
            </h4>


            {{-- Verification Result --}}

            @if($valid)

                <div class="alert alert-success">

                    <h5 class="alert-heading">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Document Verified Successfully
                    </h5>

                    <p class="mb-0">
                        This document has been signed and is currently valid.
                    </p>

                </div>

            @elseif($signed && $expired)

                <div class="alert alert-danger">

                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Document Expired
                    </h5>

                    <p class="mb-0">
                        This document was signed, but its validity period
                        has expired.
                    </p>

                </div>

            @else

                <div class="alert alert-warning">

                    <h5 class="alert-heading">
                        <i class="bi bi-clock me-2"></i>
                        Document Pending
                    </h5>

                    <p class="mb-0">
                        This document has not been signed yet.
                    </p>

                </div>

            @endif


            {{-- Document Information --}}

            <div class="table-responsive">

                <table class="table table-bordered">

                    <tr>
                        <th width="40%">Document</th>

                        <td>
                            {{ $document->file_name }}
                        </td>
                    </tr>


                    <tr>
                        <th>Signed By</th>

                        <td>
                            {{ $document->user->name ?? 'N/A' }}
                        </td>
                    </tr>


                    <tr>
                        <th>Email</th>

                        <td>
                            {{ $document->user->email ?? 'N/A' }}
                        </td>
                    </tr>


                    <tr>
                        <th>Status</th>

                        <td>

                            @if($signed)

                                <span class="badge bg-success">
                                    Signed
                                </span>

                            @else

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @endif

                        </td>
                    </tr>


                    <tr>
                        <th>Signed At</th>

                        <td>

                            @if($document->signed_at)

                                {{ $document->signed_at->format('d M Y H:i') }}

                            @else

                                <span class="text-muted">
                                    Not signed
                                </span>

                            @endif

                        </td>
                    </tr>


                    <tr>
                        <th>Expiry Date</th>

                        <td>

                            @if($document->expires_at)

                                @if($expired)

                                    <span class="text-danger fw-semibold">
                                        {{ $document->expires_at->format('d M Y') }}
                                        (Expired)
                                    </span>

                                @else

                                    {{ $document->expires_at->format('d M Y') }}

                                @endif

                            @else

                                <span class="text-muted">
                                    No expiry
                                </span>

                            @endif

                        </td>
                    </tr>


                    <tr>
                        <th>Verification ID</th>

                        <td>

                            <code class="fs-6">
                                {{ $document->verification_code }}
                            </code>

                        </td>
                    </tr>

                </table>

            </div>


            {{-- Signature --}}

            @if($signed && $document->user && $document->user->signature)

                <div class="mt-4">

                    <h5>
                        Digital Signature
                    </h5>

                    <div
                        class="border rounded p-3 bg-white mt-2"
                    >

                        <img
                            src="{{ asset('signatures/'.$document->user->signature) }}"
                            width="220"
                            class="img-fluid"
                        >

                    </div>

                </div>

            @endif


            <div class="mt-4">

                <a
                    href="{{ route('documents.verify.form') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-search me-1"></i>
                    Verify Another Document
                </a>

                @if(session()->has('authsign_id'))

                    <a
                        href="{{ route('documents.index') }}"
                        class="btn btn-secondary ms-2"
                    >
                        Back to Documents
                    </a>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection