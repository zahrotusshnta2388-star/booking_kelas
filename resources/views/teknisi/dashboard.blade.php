@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Teknisi</h1>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Ruangan</h5>
                                <h2 class="mb-0">{{ $totalRuangan }}</h2>
                            </div>
                            <i class="bi bi-building display-6"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Booking Aktif</h5>

                            </div>
                            <i class="bi bi-calendar-check display-6"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Menunggu Konfirmasi</h5>
                                <h2 class="mb-0">{{ $menungguKonfirmasi }}</h2>
                            </div>
                            <i class="bi bi-clock-history display-6"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Booking</h5>
                                <h2 class="mb-0">{{ $totalBooking }}</h2>
                            </div>
                            <i class="bi bi-calendar-week display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('teknisi.bookings.create') }}" class="btn btn-outline-success">
                                <i class="bi bi-calendar-plus"></i> Buat Booking Baru
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Booking Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @if ($bookingTerbaru->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ruangan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookingTerbaru as $booking)
                                            <tr>
                                                <td>{{ $booking->ruangan->nama }}</td>
                                                <td>{{ $booking->tanggal }}</td>
                                                <td>{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</td>
                                                <td>
                                                    @if ($booking->status == 'disetujui')
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @elseif($booking->status == 'menunggu')
                                                        <span class="badge bg-warning">Menunggu</span>
                                                    @else
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Belum ada data booking</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
