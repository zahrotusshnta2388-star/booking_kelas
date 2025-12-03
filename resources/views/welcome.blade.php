@extends('layouts.app')

@section('title', 'Home - Booking Ruangan JTI')

@push('styles')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Sistem Booking Ruangan JTI</h1>
            <p class="lead">Pinjam ruangan dan kelas dengan mudah dan efisien</p>
            <a href="{{ route('ruangan.publik') }}" class="btn btn-light btn-lg mt-3">
                <i class="bi bi-calendar3"></i> Lihat Jadwal Ruangan
            </a>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-building display-4 text-primary mb-3"></i>
                            <h3 class="card-title">20+ Ruangan</h3>
                            <p class="card-text">Ruangan dan kelas tersedia di berbagai lantai</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check display-4 text-success mb-3"></i>
                            <h3 class="card-title">Booking Online</h3>
                            <p class="card-text">Pinjam kapan saja, di mana saja secara online</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history display-4 text-warning mb-3"></i>
                            <h3 class="card-title">Real-time</h3>
                            <p class="card-text">Jadwal terupdate real-time</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
