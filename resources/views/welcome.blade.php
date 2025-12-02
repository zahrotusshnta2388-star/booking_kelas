<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Ruangan JTI</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-building"></i> Booking Ruangan JTI
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ruangan.publik') }}">Ruangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jadwal.publik') }}">Jadwal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.publik') }}">Booking</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login Teknisi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Sistem Booking Ruangan JTI</h1>
            <p class="lead mb-4">Pinjam ruangan dan kelas dengan mudah dan efisien</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card card-hover bg-white bg-opacity-10 border-0">
                                <div class="card-body">
                                    <i class="bi bi-building feature-icon mb-3"></i>
                                    <h5>20+ Ruangan</h5>
                                    <p class="small">Ruangan dan kelas tersedia</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-hover bg-white bg-opacity-10 border-0">
                                <div class="card-body">
                                    <i class="bi bi-calendar-check feature-icon mb-3"></i>
                                    <h5>Booking Online</h5>
                                    <p class="small">Pinjam kapan saja, di mana saja</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-hover bg-white bg-opacity-10 border-0">
                                <div class="card-body">
                                    <i class="bi bi-clock-history feature-icon mb-3"></i>
                                    <h5>Real-time</h5>
                                    <p class="small">Jadwal terupdate real-time</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">Fitur Utama</h2>
                    <div class="mb-4">
                        <h5><i class="bi bi-check-circle text-success me-2"></i>Lihat Ketersediaan Ruangan</h5>
                        <p>Cek jadwal dan ketersediaan ruangan secara real-time.</p>
                    </div>
                    <div class="mb-4">
                        <h5><i class="bi bi-check-circle text-success me-2"></i>Booking Online</h5>
                        <p>Ajukan peminjaman ruangan secara online tanpa ribet.</p>
                    </div>
                    <div class="mb-4">
                        <h5><i class="bi bi-check-circle text-success me-2"></i>Manajemen Mudah</h5>
                        <p>Teknisi dapat mengelola data dengan sistem CRUD lengkap.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/500x300/667eea/ffffff?text=Sistem+Booking+Ruangan" 
                         class="img-fluid rounded shadow" alt="Booking System">
                </div>
            </div>

            <div class="row text-center mb-5">
                <div class="col-md-12">
                    <h2 class="fw-bold mb-4">Mulai Sekarang</h2>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <i class="bi bi-search display-6 text-primary mb-3"></i>
                                    <h4>1. Cari Ruangan</h4>
                                    <p>Lihat daftar ruangan yang tersedia di gedung JTI</p>
                                    <a href="{{ route('ruangan.publik') }}" class="btn btn-outline-primary">
                                        Lihat Ruangan
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <i class="bi bi-calendar-check display-6 text-success mb-3"></i>
                                    <h4>2. Cek Jadwal</h4>
                                    <p>Periksa jadwal booking yang sudah ada</p>
                                    <a href="{{ route('jadwal.publik') }}" class="btn btn-outline-success">
                                        Lihat Jadwal
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <i class="bi bi-calendar-plus display-6 text-warning mb-3"></i>
                                    <h4>3. Ajukan Booking</h4>
                                    <p>Ajukan peminjaman ruangan untuk kegiatan Anda</p>
                                    <a href="{{ route('booking.publik') }}" class="btn btn-outline-warning">
                                        Buat Booking
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-building"></i> Booking Ruangan JTI</h5>
                    <p class="mb-0">Sistem peminjaman ruangan dan kelas di Gedung JTI</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Jurusan Teknologi Informasi
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>