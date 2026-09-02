@extends('layouts.app')

@section('title', 'All Documents')

@section('content')

<div class="card auth-card p-4">


    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="mb-0">

            <i class="bi bi-file-earmark-text me-2"></i>

            All Documents

        </h4>


        <a
            href="{{ route('documents.verify.form') }}"
            class="btn btn-outline-success btn-sm">

            <i class="bi bi-shield-check"></i>

            Verify Document

        </a>

    </div>


    {{-- Search + Filter + Sort --}}
    <form
        method="GET"
        action="{{ route('admin.documents') }}"
        class="row g-2 mb-4">


        {{-- Search --}}
        <div class="col-md-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search file, user or verification ID..."
                value="{{ request('search') }}">

        </div>


        {{-- Status --}}
        <div class="col-md-3">

            <select
                name="status"
                class="form-select">

                <option value="">
                    All Status
                </option>

                <option value="pending"
                    {{ request('status') == 'pending' ? 'selected' : '' }}>
                    Pending
                </option>

                <option value="signed"
                    {{ request('status') == 'signed' ? 'selected' : '' }}>
                    Signed
                </option>

            </select>

        </div>


        {{-- Sort --}}
        <div class="col-md-2">

            <select
                name="sort"
                class="form-select">

                <option value="created_at">
                    Newest
                </option>

                <option value="file_name"
                    {{ request('sort') == 'file_name' ? 'selected' : '' }}>
                    File Name
                </option>

                <option value="status"
                    {{ request('sort') == 'status' ? 'selected' : '' }}>
                    Status
                </option>

                <option value="expires_at"
                    {{ request('sort') == 'expires_at' ? 'selected' : '' }}>
                    Expiry
                </option>

                <option value="signed_at"
                    {{ request('sort') == 'signed_at' ? 'selected' : '' }}>
                    Signed Date
                </option>

            </select>

        </div>


        {{-- Direction --}}
        <div class="col-md-2">

            <select
                name="direction"
                class="form-select">

                <option value="desc"
                    {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>
                    Descending
                </option>

                <option value="asc"
                    {{ request('direction') == 'asc' ? 'selected' : '' }}>
                    Ascending
                </option>

            </select>

        </div>


        {{-- Search Button --}}
        <div class="col-md-1">

            <button
                type="submit"
                class="btn btn-primary w-100">

                <i class="bi bi-search"></i>

            </button>

        </div>

    </form>


    {{-- Documents --}}
    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th>File</th>

                    <th>User</th>

                    <th>Status</th>

                    <th>Expiry</th>

                    <th>Verification ID</th>

                    <th>Signed At</th>

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


                    {{-- User --}}
                    <td>

                        {{ $doc->user->name ?? 'N/A' }}

                        @if($doc->user)

                        <br>

                        <small class="text-muted">
                            {{ $doc->user->email }}
                        </small>

                        @endif

                    </td>


                    {{-- Status --}}
                    <td>

                        @if($doc->isExpired())

                        <span class="badge bg-danger">
                            Expired
                        </span>

                        @elseif($doc->status === 'signed')

                        <span class="badge bg-success">
                            Signed
                        </span>

                        @else

                        <span class="badge bg-warning text-dark">
                            Pending
                        </span>

                        @endif

                    </td>


                    {{-- Expiry --}}
                    <td>

                        @if($doc->expires_at)

                        @if($doc->isExpired())

                        <span class="text-danger fw-semibold">

                            {{ $doc->expires_at->format('d M Y') }}

                        </span>

                        @else

                        {{ $doc->expires_at->format('d M Y') }}

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
                            N/A
                        </span>

                        @endif

                    </td>


                    {{-- Signed At --}}
                    <td>

                        @if($doc->signed_at)

                        {{ $doc->signed_at->format('d M Y H:i') }}

                        @else

                        -

                        @endif

                    </td>


                </tr>

                @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center text-muted py-4">

                        No documents found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    <div class="mt-3">

        {{ $documents->links() }}

    </div>

</div>

@endsection