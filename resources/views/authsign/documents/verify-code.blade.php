@extends('layouts.app')

@section('title', 'Verify Document')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-6">

        <div class="card auth-card p-4">

            <div class="text-center mb-4">

                <div class="fs-1">
                    <i class="bi bi-shield-check"></i>
                </div>

                <h3>
                    Verify Digital Document
                </h3>

                <p class="text-muted">
                    Enter the unique verification ID of the document
                    to check its authenticity and validity.
                </p>

            </div>


            {{-- Error --}}
            @if(session('error'))

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-circle me-1"></i>

                    {{ session('error') }}

                </div>

            @endif


            {{-- Validation errors --}}
            @if($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('documents.verify.code') }}"
            >

                @csrf


                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Document Verification ID
                    </label>

                    <input
                        type="text"
                        name="verification_code"
                        class="form-control form-control-lg text-center"
                        placeholder="Example: DS-A8K92P4X7M"
                        value="{{ old('verification_code') }}"
                        required
                        autofocus
                    >

                    <div class="form-text">
                        Enter the verification ID provided with the
                        signed document.
                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-success w-100"
                >
                    <i class="bi bi-search me-1"></i>
                    Verify Document
                </button>

            </form>


            @if(session()->has('authsign_id'))

                <div class="text-center mt-3">

                    <a href="{{ route('documents.index') }}">
                        Back to My Documents
                    </a>

                </div>

            @else

                <div class="text-center mt-3">

                    <a href="{{ route('login.form') }}">
                        Back to Login
                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection