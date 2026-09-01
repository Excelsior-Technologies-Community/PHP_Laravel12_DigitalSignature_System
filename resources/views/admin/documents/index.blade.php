@extends('layouts.app')

@section('title', 'All Documents')

@section('content')

<div class="card auth-card p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>
            <i class="bi bi-file-earmark-text me-2"></i>
            All Documents
        </h4>

        <a
            href="{{ route('documents.verify.form') }}"
            class="btn btn-outline-success btn-sm"
        >
            <i class="bi bi-shield-check"></i>
            Verify Document
        </a>

    </div>


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

                        <td>
                            {{ $doc->file_name }}
                        </td>


                        <td>
                            {{ $doc->user->name ?? 'N/A' }}
                        </td>


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


                        <td>

                            {{ $doc->signed_at
                                ? $doc->signed_at->format('d M Y H:i')
                                : '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted"
                        >
                            No documents found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="mt-3">

        {{ $documents->links() }}

    </div>

</div>

@endsection