<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Ruangan - Booking Ruangan JTI</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .ruangan-img {
            height: 200px;
            object-fit: cover;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .fasilitas-list {
            list-style: none;
            padding-left: 0;
        }
        .fasilitas-list li {
            margin-bottom: 5px;
        }
        .hero-ruangan {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
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
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('ruangan.publik') }}">Ruangan</a>
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
    <section class="hero-ruangan">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold">Daftar Ruangan JTI</h1>
                    <p class="lead">Temukan ruangan yang sesuai dengan kebutuhan kegiatan Anda</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('booking.publik') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-calendar-plus"></i> Ajukan Booking
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Filter dan Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Total {{ $ruangans->count() }} ruangan tersedia</strong> untuk peminjaman
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">Semua</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="gedung-a">Gedung A</button>
                    <button type="button" class="btn btn-outline-primary" data-filter="gedung-b">Gedung B</button>
                </div>
            </div>
        </div>

        <!-- Daftar Ruangan -->
        <div class="row" id="ruangan-list">
            @forelse($ruangans as $ruangan)
            <div class="col-lg-4 col-md-6 mb-4 ruangan-item" data-gedung="{{ strtolower($ruangan->gedung) }}">
                <div class="card card-hover h-100">
                    <!-- Status Badge -->
                    <span class="badge status-badge {{ $ruangan->status == 'tersedia' ? 'bg-success' : 'bg-danger' }}">
                        {{ $ruangan->status == 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                    
                    <!-- Gambar Ruangan -->
                    <img src="https://via.placeholder.com/400x200/{{ $ruangan->status == 'tersedia' ? '28a745' : 'dc3545' }}/ffffff?text=Ruangan+{{ $ruangan->kode }}" 
                         class="card-img-top ruangan-img" alt="Ruangan {{ $ruangan->nama }}">
                    
                    <div class="card-body">
                        <!-- Kode dan Nama -->
                        <h5 class="card-title mb-1">{{ $ruangan->nama }}</h5>
                        <span class="badge bg-primary mb-3">{{ $ruangan->kode }}</span>
                        
                        <!-- Info Dasar -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Gedung</small>
                                <strong><i class="bi bi-building"></i> {{ $ruangan->gedung }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Lantai</small>
                                <strong><i class="bi bi-stairs"></i> {{ $ruangan->lantai }}</strong>
                            </div>
                        </div>
                        
                        <!-- Kapasitas -->
                        <div class="mb-3">
                            <small class="text-muted d-block">Kapasitas</small>
                            <strong><i class="bi bi-people"></i> {{ $ruangan->kapasitas }} orang</strong>
                        </div>
                        
                        <!-- Fasilitas -->
                        @if($ruangan->fasilitas)
                        <div class="mb-3">
                            <small class="text-muted d-block">Fasilitas</small>
                            <ul class="fasilitas-list mb-0">
                                @php
                                    $fasilitas = json_decode($ruangan->fasilitas, true) ?? [];
                                @endphp
                                @foreach(array_slice($fasilitas, 0, 3) as $fasil)
                                    <li><i class="bi bi-check-circle text-success me-1"></i> {{ $fasil }}</li>
                                @endforeach
                                @if(count($fasilitas) > 3)
                                    <li><small class="text-muted">+{{ count($fasilitas) - 3 }} fasilitas lainnya</small></li>
                                @endif
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Deskripsi -->
                        @if($ruangan->deskripsi)
                        <div class="mb-3">
                            <small class="text-muted d-block">Deskripsi</small>
                            <p class="mb-0 small">{{ Str::limit($ruangan->deskripsi, 100) }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            @if($ruangan->status == 'tersedia')
                            <a href="{{ route('booking.publik') }}?ruangan={{ $ruangan->id }}" 
                               class="btn btn-primary">
                                <i class="bi bi-calendar-plus"></i> Booking Sekarang
                            </a>
                            @else
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-calendar-x"></i> Tidak Tersedia
                            </button>
                            @endif
                            <a href="#" class="btn btn-outline-secondary" 
                               data-bs-toggle="modal" 
                               data-bs-target="#detailRuangan{{ $ruangan->id }}">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailRuangan{{ $ruangan->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Ruangan {{ $ruangan->nama }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="https://via.placeholder.com/500x300/667eea/ffffff?text=Ruangan+{{ $ruangan->kode }}" 
                                         class="img-fluid rounded mb-3" alt="Ruangan {{ $ruangan->nama }}">
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Kode Ruangan</th>
                                            <td>: <span class="badge bg-primary">{{ $ruangan->kode }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Ruangan</th>
                                            <td>: {{ $ruangan->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi</th>
                                            <td>: Gedung {{ $ruangan->gedung }}, Lantai {{ $ruangan->lantai }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kapasitas</th>
                                            <td>: {{ $ruangan->kapasitas }} orang</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>: 
                                                <span class="badge {{ $ruangan->status == 'tersedia' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $ruangan->status == 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($ruangan->deskripsi)
                            <div class="mt-3">
                                <h6>Deskripsi</h6>
                                <p>{{ $ruangan->deskripsi }}</p>
                            </div>
                            @endif
                            
                            @if($ruangan->fasilitas)
                            <div class="mt-3">
                                <h6>Fasilitas</h6>
                                <div class="row">
                                    @php
                                        $fasilitas = json_decode($ruangan->fasilitas, true) ?? [];
                                    @endphp
                                    @foreach($fasilitas as $fasil)
                                    <div class="col-md-6 mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i> {{ $fasil }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            @if($ruangan->status == 'tersedia')
                            <a href="{{ route('booking.publik') }}?ruangan={{ $ruangan->id }}" 
                               class="btn btn-primary">
                                <i class="bi bi-calendar-plus"></i> Booking Ruangan Ini
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning text-center py-5">
                    <i class="bi bi-building-slash display-1"></i>
                    <h3 class="mt-3">Belum ada ruangan tersedia</h3>
                    <p class="mb-0">Silakan hubungi teknisi untuk informasi lebih lanjut.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-building"></i> Booking Ruangan JTI</h5>
                    <p class="mb-0">Sistem peminjaman ruangan dan kelas di Gedung JTI</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Jurusan Teknologi Informasi<br>
                        <small>Versi 1.0</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk Filter -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('[data-filter]');
            const ruanganItems = document.querySelectorAll('.ruangan-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    // Filter ruangan
                    ruanganItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-gedung').includes(filter)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>