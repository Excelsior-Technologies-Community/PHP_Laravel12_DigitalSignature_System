@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')

<div class="card auth-card p-4">


    {{-- Header --}}
    <div class="mb-4">

        <h4>

            <i class="bi bi-clock-history me-2"></i>

            Activity Logs

        </h4>

    </div>


    {{-- Search --}}
    <form
        method="GET"
        action="{{ route('admin.activity.logs') }}"
        class="row g-2 mb-4">

        <div class="col-md-7">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search user, email, action or IP..."
                value="{{ request('search') }}">

        </div>


        <div class="col-md-3">

            <select
                name="action"
                class="form-select">

                <option value="">
                    All Actions
                </option>

                <option value="Registered"
                    {{ request('action') == 'Registered' ? 'selected' : '' }}>
                    Registered
                </option>

                <option value="Logged out"
                    {{ request('action') == 'Logged out' ? 'selected' : '' }}>
                    Logged out
                </option>

                <option value="Signature"
                    {{ request('action') == 'Signature' ? 'selected' : '' }}>
                    Signature
                </option>

                <option value="Document"
                    {{ request('action') == 'Document' ? 'selected' : '' }}>
                    Document
                </option>

                <option value="Toggled admin"
                    {{ request('action') == 'Toggled admin' ? 'selected' : '' }}>
                    Admin Changes
                </option>

                <option value="Deleted user"
                    {{ request('action') == 'Deleted user' ? 'selected' : '' }}>
                    Deleted User
                </option>

            </select>

        </div>


        <div class="col-md-2">

            <button
                type="submit"
                class="btn btn-primary w-100">

                <i class="bi bi-search"></i>

                Search

            </button>

        </div>

    </form>


    {{-- Logs Table --}}
    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th>User</th>

                    <th>Action</th>

                    <th>IP Address</th>

                    <th>Date</th>

                </tr>

            </thead>


            <tbody>

                @forelse($logs as $log)

                <tr>


                    {{-- User --}}
                    <td>

                        @if($log->user)

                        <strong>
                            {{ $log->user->name }}
                        </strong>

                        <br>

                        <small class="text-muted">
                            {{ $log->user->email }}
                        </small>

                        @else

                        <span class="text-muted">
                            Unknown User
                        </span>

                        @endif

                    </td>


                    {{-- Action --}}
                    <td>

                        {{ $log->action }}

                    </td>


                    {{-- IP --}}
                    <td>

                        <code>
                            {{ $log->ip_address ?? '-' }}
                        </code>

                    </td>


                    {{-- Date --}}
                    <td>

                        {{ $log->created_at
                                ? $log->created_at->format('d M Y H:i:s')
                                : '-' }}

                    </td>


                </tr>

                @empty

                <tr>

                    <td
                        colspan="4"
                        class="text-center text-muted py-4">

                        No activity logs found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    <div class="mt-3">

        {{ $logs->links() }}

    </div>

</div>

@endsection