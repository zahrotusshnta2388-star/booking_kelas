@extends('layouts.app')

@section('title', 'Jadwal Ruangan - Booking Ruangan JTI')

@push('styles')
    <style>
        /* CSS khusus halaman ruangan (pindahkan dari style tag) */
        .hero-ruangan {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }

        .hero-ruangan {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }

        .time-table {
            border-collapse: collapse;
            width: 100%;
        }

        .time-table th,
        .time-table td {
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            font-size: 0.8rem;
        }

        .time-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
            font-weight: bold;
            min-width: 60px;
        }

        .ruangan-name {
            background-color: #e3f2fd;
            font-weight: bold;
            text-align: left;
            padding-left: 10px !important;
            position: sticky;
            left: 0;
            z-index: 5;
            min-width: 180px;
        }

        .booking-cell {
            background-color: #d4edda;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .booking-cell:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .booking-cell.pending {
            background-color: #fff3cd;
        }

        .empty-cell {
            background-color: #f8f9fa;
        }

        .floor-section {
            border-top: 3px solid #0d6efd;
            margin-top: 20px;
            padding-top: 15px;
        }

        .floor-title {
            background-color: #0d6efd;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .time-slot-header {
            background-color: #6c757d !important;
            color: white;
            font-size: 0.75rem;
        }

        .legend {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }

        .color-approved {
            background-color: #d4edda;
        }

        .color-pending {
            background-color: #fff3cd;
        }

        .color-empty {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ruangan-name {
                min-width: 150px;
                font-size: 0.75rem;
            }

            .time-table th,
            .time-table td {
                font-size: 0.7rem;
                padding: 2px 1px;
                min-width: 45px;
            }
        }
    </style>
@endpush


@section('content')
    <!-- Hero Section -->
    <section class="hero-ruangan">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold">Jadwal Ruangan JTI</h1>
                    <p class="lead">Jadwal peminjaman ruangan per jam untuk hari ini ({{ date('d/m/Y') }})</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="btn-group" role="group">
                        <button type="button" id="btnToday" class="btn btn-light">
                            <i class="bi bi-calendar-day"></i> Hari Ini
                        </button>
                        <button type="button" id="btnTomorrow" class="btn btn-outline-light">
                            <i class="bi bi-calendar-plus"></i> Besok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter dan Kontrol -->
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="dateFilter" class="form-label">Pilih Tanggal:</label>
                <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-8">
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color color-approved"></div>
                        <span>Disetujui</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color color-pending"></div>
                        <span>Menunggu</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color color-empty"></div>
                        <span>Kosong/Tersedia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <div class="container">
        <!-- Legenda dan Info -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Petunjuk:</strong> Klik pada kotak berwarna untuk melihat detail booking. Ruangan tersedia jika
            kotak berwarna abu-abu.
        </div>

        <!-- Lantai 2 -->
        <div class="floor-section" id="floor2">
            <h3 class="floor-title">Lantai 2</h3>
            <div class="table-container">
                <table class="time-table">
                    <thead>
                        <tr>
                            <th class="ruangan-name">Ruangan / Jam</th>
                            @foreach ($jamSlots as $jam)
                                <th class="time-slot-header">{{ $jam }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ruangans->where('lantai', 2) as $ruangan)
                            @php
                                $ruanganBookings = isset($bookingMap[$ruangan->id]) ? $bookingMap[$ruangan->id] : [];
                            @endphp
                            <tr>
                                <td class="ruangan-name" title="{{ $ruangan->deskripsi }}">
                                    {{ $ruangan->nama }}
                                    <br>
                                    <small class="text-muted">Kapasitas: {{ $ruangan->kapasitas }} orang</small>
                                </td>

                                @foreach ($jamSlots as $jam)
                                    @php
                                        $hasBooking = isset($ruanganBookings[$jam]);
                                        $booking = $hasBooking ? $ruanganBookings[$jam]['booking'] : null;
                                        $isStartSlot =
                                            $hasBooking &&
                                            $ruanganBookings[$jam]['startSlot'] == (int) substr($jam, 0, 2);
                                    @endphp

                                    @if ($hasBooking && $isStartSlot)
                                        @php
                                            $span = $ruanganBookings[$jam]['span'];
                                            $statusClass =
                                                $booking->status == 'disetujui'
                                                    ? 'booking-cell'
                                                    : 'booking-cell pending';
                                        @endphp
                                        <td class="{{ $statusClass }}" colspan="{{ $span }}" data-bs-toggle="modal"
                                            data-bs-target="#detailModal" data-booking='@json($booking)'
                                            title="{{ $booking->nama_peminjam }} - {{ $booking->keperluan }}">
                                            <div class="fw-bold">{{ $booking->nama_peminjam }}</div>
                                            <small>{{ $booking->keperluan }}</small>
                                            <div
                                                class="badge badge-sm {{ $booking->status == 'disetujui' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $booking->status == 'disetujui' ? '✓' : '⏳' }}
                                            </div>
                                        </td>
                                    @elseif($hasBooking && !$isStartSlot)
                                        <!-- Kosongkan cell karena sudah di-cover oleh colspan -->
                                    @else
                                        <td class="empty-cell">
                                            <span class="text-muted">-</span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lantai 3 -->
        <div class="floor-section" id="floor3">
            <h3 class="floor-title">Lantai 3</h3>
            <div class="table-container">
                <table class="time-table">
                    <thead>
                        <tr>
                            <th class="ruangan-name">Ruangan / Jam</th>
                            @foreach ($jamSlots as $jam)
                                <th class="time-slot-header">{{ $jam }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ruangans->where('lantai', 3) as $ruangan)
                            @php
                                $ruanganBookings = isset($bookingMap[$ruangan->id]) ? $bookingMap[$ruangan->id] : [];
                            @endphp
                            <tr>
                                <td class="ruangan-name" title="{{ $ruangan->deskripsi }}">
                                    {{ $ruangan->nama }}
                                    <br>
                                    <small class="text-muted">Kapasitas: {{ $ruangan->kapasitas }} orang</small>
                                </td>

                                @foreach ($jamSlots as $jam)
                                    @php
                                        $hasBooking = isset($ruanganBookings[$jam]);
                                        $booking = $hasBooking ? $ruanganBookings[$jam]['booking'] : null;
                                        $isStartSlot =
                                            $hasBooking &&
                                            $ruanganBookings[$jam]['startSlot'] == (int) substr($jam, 0, 2);
                                    @endphp

                                    @if ($hasBooking && $isStartSlot)
                                        @php
                                            $span = $ruanganBookings[$jam]['span'];
                                            $statusClass =
                                                $booking->status == 'disetujui'
                                                    ? 'booking-cell'
                                                    : 'booking-cell pending';
                                        @endphp
                                        <td class="{{ $statusClass }}" colspan="{{ $span }}"
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-booking='@json($booking)'
                                            title="{{ $booking->nama_peminjam }} - {{ $booking->keperluan }}">
                                            <div class="fw-bold">{{ $booking->nama_peminjam }}</div>
                                            <small>{{ $booking->keperluan }}</small>
                                            <div
                                                class="badge badge-sm {{ $booking->status == 'disetujui' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $booking->status == 'disetujui' ? '✓' : '⏳' }}
                                            </div>
                                        </td>
                                    @elseif($hasBooking && !$isStartSlot)
                                        <!-- Kosongkan cell karena sudah di-cover oleh colspan -->
                                    @else
                                        <td class="empty-cell">
                                            <span class="text-muted">-</span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lantai 4 -->
        <div class="floor-section" id="floor4">
            <h3 class="floor-title">Lantai 4</h3>
            <div class="table-container">
                <table class="time-table">
                    <thead>
                        <tr>
                            <th class="ruangan-name">Ruangan / Jam</th>
                            @foreach ($jamSlots as $jam)
                                <th class="time-slot-header">{{ $jam }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ruangans->where('lantai', 4) as $ruangan)
                            @php
                                $ruanganBookings = isset($bookingMap[$ruangan->id]) ? $bookingMap[$ruangan->id] : [];
                            @endphp
                            <tr>
                                <td class="ruangan-name" title="{{ $ruangan->deskripsi }}">
                                    {{ $ruangan->nama }}
                                    <br>
                                    <small class="text-muted">Kapasitas: {{ $ruangan->kapasitas }} orang</small>
                                </td>

                                @foreach ($jamSlots as $jam)
                                    @php
                                        $hasBooking = isset($ruanganBookings[$jam]);
                                        $booking = $hasBooking ? $ruanganBookings[$jam]['booking'] : null;
                                        $isStartSlot =
                                            $hasBooking &&
                                            $ruanganBookings[$jam]['startSlot'] == (int) substr($jam, 0, 2);
                                    @endphp

                                    @if ($hasBooking && $isStartSlot)
                                        @php
                                            $span = $ruanganBookings[$jam]['span'];
                                            $statusClass =
                                                $booking->status == 'disetujui'
                                                    ? 'booking-cell'
                                                    : 'booking-cell pending';
                                        @endphp
                                        <td class="{{ $statusClass }}" colspan="{{ $span }}"
                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                            data-booking='@json($booking)'
                                            title="{{ $booking->nama_peminjam }} - {{ $booking->keperluan }}">
                                            <div class="fw-bold">{{ $booking->nama_peminjam }}</div>
                                            <small>{{ $booking->keperluan }}</small>
                                            <div
                                                class="badge badge-sm {{ $booking->status == 'disetujui' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $booking->status == 'disetujui' ? '✓' : '⏳' }}
                                            </div>
                                        </td>
                                    @elseif($hasBooking && !$isStartSlot)
                                        <!-- Kosongkan cell karena sudah di-cover oleh colspan -->
                                    @else
                                        <td class="empty-cell">
                                            <span class="text-muted">-</span>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Detail akan diisi via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
                                if (filter === 'all' || item.getAttribute('data-gedung').includes(
                                        filter)) {
                                    item.style.display = 'block';
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                        });
                    });
    </script>
@endpush
