@extends('layouts.app')

@section('title', 'Sign Document')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-7">

        <div class="card auth-card p-4">

            <h4 class="mb-4">
                <i class="bi bi-pen me-2"></i>
                Sign Document
            </h4>


            {{-- DOCUMENT DETAILS --}}
            <div class="mb-3">

                <p>
                    <strong>File:</strong>
                    {{ $document->file_name }}
                </p>

                <p>
                    <strong>Status:</strong>

                    <span class="badge bg-warning text-dark">
                        {{ ucfirst($document->status) }}
                    </span>
                </p>


                <p>
                    <strong>Expiry:</strong>

                    @if($document->expires_at)

                        {{ $document->expires_at->format('d M Y') }}

                    @else

                        <span class="text-muted">
                            No expiry
                        </span>

                    @endif

                </p>


                <p>
                    <strong>Verification ID:</strong>

                    <code>
                        {{ $document->verification_code }}
                    </code>
                </p>

            </div>


            {{-- EXPIRED DOCUMENT --}}
            @if($document->isExpired())

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-triangle me-2"></i>

                    <strong>Document Expired</strong>

                    <p class="mb-0 mt-1">
                        This document has expired and cannot be signed.
                    </p>

                </div>


                <a
                    href="{{ route('documents.index') }}"
                    class="btn btn-secondary"
                >
                    Back to Documents
                </a>


            @else


                {{-- CHECK SIGNATURE --}}
                @if(!$user->signature)

                    <div class="alert alert-warning">

                        <i class="bi bi-exclamation-triangle me-2"></i>

                        <strong>No digital signature found.</strong>

                        <p class="mb-2 mt-1">
                            You must create a digital signature before
                            signing this document.
                        </p>

                        <a
                            href="{{ route('signature.form') }}"
                            class="btn btn-primary btn-sm"
                        >
                            Create Signature
                        </a>

                    </div>


                    <a
                        href="{{ route('documents.index') }}"
                        class="btn btn-secondary"
                    >
                        Back to Documents
                    </a>


                @else


                    {{-- INFORMATION --}}
                    <div class="alert alert-info">

                        <i class="bi bi-info-circle me-2"></i>

                        Your saved digital signature will be used to sign
                        this document.

                    </div>


                    {{-- SIGNATURE PREVIEW --}}
                    <div class="mb-4">

                        <label class="fw-semibold">
                            Your Signature
                        </label>

                        <div
                            class="border rounded p-3 mt-2 bg-white text-center"
                        >

                            <img
                                src="{{ asset('signatures/' . $user->signature) }}"
                                alt="Your Digital Signature"
                                style="
                                    max-width: 300px;
                                    max-height: 150px;
                                    object-fit: contain;
                                "
                                class="img-fluid"
                            >

                        </div>

                        <small class="text-muted">
                            Current signature:
                            {{ $user->signature }}
                        </small>

                    </div>


                    {{-- SIGN FORM --}}
                    <form
                        method="POST"
                        action="{{ route('documents.sign.save', $document->id) }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success"
                            onclick="return confirm('Are you sure you want to sign this document?')"
                        >

                            <i class="bi bi-pen me-1"></i>

                            Sign This Document

                        </button>


                        <a
                            href="{{ route('documents.index') }}"
                            class="btn btn-secondary ms-2"
                        >
                            Cancel
                        </a>

                    </form>

                @endif

            @endif

        </div>

    </div>

</div>

@endsection

