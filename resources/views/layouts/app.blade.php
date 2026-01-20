<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Digital Signature System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
        }

        .auth-card {
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        footer {
            background: #fff;
            border-top: 1px solid #e5e5e5;
            margin-top: 60px;
        }

        .signature-box {
            border: 2px dashed #000;
            background: #fff;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- 🔹 NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            ✍️ Digital Signature
        </a>

        @if(session()->has('authsign_id'))
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="fw-semibold">
                    Hi, {{ session('authsign_name') }}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger">
                    Logout
                </a>
            </div>
        @endif
    </div>
</nav>

<!-- 🔹 MAIN CONTENT -->
<main class="container my-5">
    @yield('content')
</main>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
