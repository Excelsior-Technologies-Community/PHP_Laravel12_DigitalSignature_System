@extends('layouts.app')

@section('title', 'Users')

@section('content')

<div class="card auth-card p-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Users
        </h4>

    </div>


    {{-- Search + Sorting --}}
    <form
        method="GET"
        action="{{ route('admin.users') }}"
        class="row g-2 mb-4">

        {{-- Search --}}
        <div class="col-md-5">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search name or email..."
                value="{{ request('search') }}">

        </div>


        {{-- Sort --}}
        <div class="col-md-3">

            <select
                name="sort"
                class="form-select">

                <option value="created_at"
                    {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>
                    Registered Date
                </option>

                <option value="name"
                    {{ request('sort') == 'name' ? 'selected' : '' }}>
                    Name
                </option>

                <option value="email"
                    {{ request('sort') == 'email' ? 'selected' : '' }}>
                    Email
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


        {{-- Button --}}
        <div class="col-md-2">

            <button
                type="submit"
                class="btn btn-primary w-100">
                <i class="bi bi-search"></i>
                Search
            </button>

        </div>

    </form>


    {{-- Users Table --}}
    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Admin</th>

                    <th>Registered</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                @forelse($users as $user)

                <tr>

                    {{-- Name --}}
                    <td>
                        {{ $user->name }}
                    </td>


                    {{-- Email --}}
                    <td>
                        {{ $user->email }}
                    </td>


                    {{-- Admin --}}
                    <td>

                        @if($user->is_admin)

                        <span class="badge bg-success">
                            Admin
                        </span>

                        @else

                        <span class="badge bg-secondary">
                            User
                        </span>

                        @endif

                    </td>


                    {{-- Registered --}}
                    <td>

                        {{ $user->created_at
                                ? $user->created_at->format('d M Y')
                                : '-' }}

                    </td>


                    {{-- Actions --}}
                    <td>

                        <div class="d-flex gap-2">


                            {{-- Toggle Admin --}}
                            @if((int) $user->id !== (int) session('authsign_id'))

                            <form
                                action="{{ route(
                                            'admin.users.toggleAdmin',
                                            $user->id
                                        ) }}"
                                method="POST">

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-{{ $user->is_admin ? 'warning' : 'success' }}">

                                    @if($user->is_admin)
                                    Remove Admin
                                    @else
                                    Make Admin
                                    @endif

                                </button>

                            </form>

                            @endif


                            {{-- Delete User --}}
                            @if((int) $user->id !== (int) session('authsign_id'))

                            <form
                                action="{{ route(
                                            'admin.users.delete',
                                            $user->id
                                        ) }}"
                                method="POST"
                                onsubmit="return confirm(
                                            'Are you sure you want to delete this user? All signatures, documents and activity logs will also be deleted.'
                                        );">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger">

                                    <i class="bi bi-trash"></i>
                                    Delete

                                </button>

                            </form>

                            @endif

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="5"
                        class="text-center text-muted py-4">

                        No users found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    <div class="mt-3">

        {{ $users->links() }}

    </div>

</div>

@endsection