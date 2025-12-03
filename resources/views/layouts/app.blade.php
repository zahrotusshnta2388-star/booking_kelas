{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Booking Ruangan JTI')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding-bottom: 20px;
        }

        /* Footer styles */
        .footer {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
        }

        .footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: white;
            text-decoration: underline;
        }

        .footer .social-links a {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .footer .social-links a:hover {
            color: var(--primary-color);
        }

        .footer .list-unstyled li {
            margin-bottom: 8px;
        }

        .footer .system-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .footer .border-light {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Simple footer variant */
        .footer.bg-white {
            background: white !important;
            color: #6c757d !important;
        }

        .footer.bg-white a {
            color: #6c757d;
        }

        .footer.bg-white a:hover {
            color: var(--primary-color);
        }

        /* Active nav link */
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        /* Print styles */
        @media print {

            .footer,
            .navbar {
                display: none !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .footer {
                text-align: center;
                padding: 30px 0 15px;
            }

            .footer .text-md-start,
            .footer .text-md-end {
                text-align: center !important;
            }
        }
    </style>

    <!-- Additional styles per page -->
    @stack('styles')
</head>

<body>
    <!-- Navbar Component -->
    @if (!isset($hideNavbar) || !$hideNavbar)
        <x-navbar :activePage="$activePage ?? ''" />
    @endif

    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="container mt-3">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Footer Component -->
    @if (!isset($hideFooter) || !$hideFooter)
        <x-footer :simple="$simpleFooter ?? false" />
    @endif

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional scripts per page -->
    @stack('scripts')
</body>

</html>
