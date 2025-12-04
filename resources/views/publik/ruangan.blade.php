@extends('layouts.app')

@section('title', 'Jadwal Ruangan - Booking Ruangan JTI')

@push('styles')
    <style>
        /* CSS khusus halaman ruangan */
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
            padding: 8px 4px;
            font-size: 0.85rem;
        }

        .time-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
            font-weight: bold;
            min-width: 70px;
        }

        .ruangan-name {
            background-color: #e3f2fd;
            font-weight: bold;
            text-align: left;
            padding-left: 15px !important;
            position: sticky;
            left: 0;
            z-index: 5;
            min-width: 200px;
        }

        .booking-cell {
            background-color: #d4edda !important;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .booking-cell:hover {
            transform: scale(1.02);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .booking-cell.pending {
            background-color: #fff3cd !important;
        }

        .empty-cell {
            background-color: #f8f9fa;
        }

        .floor-section {
            border-top: 3px solid #0d6efd;
            margin-top: 25px;
            padding-top: 20px;
        }

        .floor-title {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: inline-block;
            font-size: 1.2rem;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .time-slot-header {
            background-color: #6c757d !important;
            color: white;
            font-size: 0.8rem;
            padding: 10px 5px;
        }

        .legend {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 25px;
            height: 25px;
            border-radius: 4px;
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
                min-width: 160px;
                font-size: 0.8rem;
            }

            .time-table th,
            .time-table td {
                font-size: 0.75rem;
                padding: 6px 3px;
                min-width: 50px;
            }

            .floor-title {
                font-size: 1rem;
                padding: 8px 15px;
            }
        }

        /* Style untuk konten booking */
        .booking-content {
            padding: 5px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .booking-info {
            text-align: center;
        }

        .booking-name {
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 3px;
            color: #333;
        }

        .booking-time {
            font-size: 0.75rem;
            color: #666;
            margin-bottom: 3px;
        }

        .booking-purpose {
            font-size: 0.75rem;
            color: #555;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .booking-status {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }

        /* Tombol aksi */
        .booking-actions {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            gap: 3px;
        }

        .booking-actions .btn {
            padding: 3px 6px !important;
            font-size: 0.7rem !important;
        }

        /* Fix untuk modal yang stuck */
        .modal.fade .modal-dialog {
            transform: translate(0, 0);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: none;
        }
    </style>
@endpush


@section('content')
    <!-- HERO SECTION -->
    <section class="hero-ruangan">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold">Jadwal Ruangan JTI</h1>
                    <p class="lead">Jadwal peminjaman ruangan per jam untuk
                        {{ \Carbon\Carbon::parse($selectedDate ?? now())->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="btn-group" role="group">
                        <button type="button" id="btnToday" class="btn btn-light"
                            onclick="window.location.href='{{ url()->current() }}?date={{ date('Y-m-d') }}'">
                            <i class="bi bi-calendar-day"></i> Hari Ini
                        </button>
                        <button type="button" id="btnTomorrow" class="btn btn-outline-light"
                            onclick="window.location.href='{{ url()->current() }}?date={{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}'">
                            <i class="bi bi-calendar-plus"></i> Besok
                        </button>
                        @auth
                            @if (Auth::user()->role === 'teknisi')
                                <a href="{{ route('ruangan.index') }}" class="btn btn-outline-light">
                                    <i class="bi bi-gear"></i> Admin
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTER DAN KONTROL -->
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <form method="GET" action="{{ url()->current() }}" id="dateForm">
                    <label for="dateFilter" class="form-label">Pilih Tanggal:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="dateFilter" name="date"
                            value="{{ $selectedDate ?? date('Y-m-d') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>
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

    <!-- TABEL JADWAL -->
    <div class="container">
        <div class="alert alert-light border">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Petunjuk:</strong> Klik pada kotak berwarna untuk melihat detail booking.
            Booking akan menampilkan dari jam mulai sampai jam selesai.
        </div>

        @php
            $floors = [2, 3, 4];
        @endphp

        @foreach ($floors as $floor)
            @if ($ruangans->where('lantai', $floor)->count() > 0)
                <div class="floor-section" id="floor{{ $floor }}">
                    <h3 class="floor-title">Lantai {{ $floor }}</h3>
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
                                @foreach ($ruangans->where('lantai', $floor) as $ruangan)
                                    @php
                                        // Ambil booking untuk ruangan ini
                                        $ruanganBookings = $bookingMap[$ruangan->id] ?? [];
                                        $ruanganOccupied = $occupiedSlots[$ruangan->id] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="ruangan-name" title="{{ $ruangan->deskripsi ?? '' }}">
                                            <strong>{{ $ruangan->nama }}</strong>
                                            <br>
                                            <small class="text-muted">Kapasitas: {{ $ruangan->kapasitas }} orang</small>
                                            @if ($ruangan->fasilitas)
                                                <br>
                                                @php
                                                    // Handle berbagai format fasilitas
                                                    $fasilitasText = '';
                                                    if (is_string($ruangan->fasilitas)) {
                                                        // Coba decode JSON
                                                        $decoded = json_decode($ruangan->fasilitas, true);
                                                        if (
                                                            json_last_error() === JSON_ERROR_NONE &&
                                                            is_array($decoded)
                                                        ) {
                                                            $fasilitasText = implode(', ', $decoded);
                                                        } else {
                                                            $fasilitasText = $ruangan->fasilitas;
                                                        }
                                                    } elseif (is_array($ruangan->fasilitas)) {
                                                        $fasilitasText = implode(', ', $ruangan->fasilitas);
                                                    }
                                                @endphp
                                                @if (!empty($fasilitasText))
                                                    <small class="text-muted">{{ Str::limit($fasilitasText, 30) }}</small>
                                                @endif
                                            @endif
                                        </td>

                                        @foreach ($jamSlots as $jam)
                                            @php
                                                // Cek apakah slot ini adalah awal dari booking
                                                $isStartSlot = isset($ruanganBookings[$jam]);
                                                $booking = $isStartSlot ? $ruanganBookings[$jam]['booking'] : null;
                                                $span = $isStartSlot ? $ruanganBookings[$jam]['span'] ?? 1 : 1;

                                                // Cek apakah slot ini bagian dari booking (tapi bukan awal)
                                                $isOccupiedSlot = isset($ruanganOccupied[$jam]) && !$isStartSlot;
                                            @endphp

                                            @if ($isStartSlot)
                                                @php
                                                    $statusClass =
                                                        ($booking->status ?? '') == 'disetujui'
                                                            ? 'booking-cell'
                                                            : 'booking-cell pending';
                                                    $jamDisplay =
                                                        ($ruanganBookings[$jam]['jam_mulai_display'] ??
                                                            substr($booking->jam_mulai, 0, 5)) .
                                                        ' - ' .
                                                        ($ruanganBookings[$jam]['jam_selesai_display'] ??
                                                            substr($booking->jam_selesai, 0, 5));
                                                @endphp

                                                <td class="{{ $statusClass }}" colspan="{{ $span }}"
                                                    data-booking-id="{{ $booking->id ?? '' }}"
                                                    title="{{ $booking->nama_peminjam ?? '' }} - {{ $booking->keperluan ?? '' }}">

                                                    <div class="booking-content">
                                                        <div class="booking-info">
                                                            <div class="booking-name">
                                                                {{ Str::limit($booking->nama_peminjam ?? '', 12) }}</div>
                                                            <div class="booking-time">{{ $jamDisplay }}</div>
                                                            <div class="booking-purpose">
                                                                {{ Str::limit($booking->keperluan ?? '', 20) }}</div>
                                                            <span
                                                                class="booking-status badge {{ $booking->status == 'disetujui' ? 'bg-success' : 'bg-warning' }}">
                                                                {{ $booking->status == 'disetujui' ? '✓ Disetujui' : '⏳ Menunggu' }}
                                                            </span>
                                                        </div>

                                                        <div class="booking-actions">
                                                            <!-- Tombol Detail -->
                                                            <button type="button" class="btn btn-info btn-sm"
                                                                onclick="event.stopPropagation(); showBookingDetail({{ $booking->id ?? 'null' }})"
                                                                title="Lihat Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </button>

                                                            @if (Auth::check() && ($booking->user_id == Auth::id() || Auth::user()->role === 'teknisi'))
                                                                <!-- Tombol Edit -->
                                                                <button type="button" class="btn btn-warning btn-sm"
                                                                    onclick="event.stopPropagation(); editBooking({{ $booking->id ?? 'null' }})"
                                                                    title="Edit Booking">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>

                                                                <!-- Tombol Hapus -->
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="event.stopPropagation(); deleteBooking({{ $booking->id ?? 'null' }})"
                                                                    title="Hapus Booking">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>

                                                                <!-- Tombol Approve (hanya untuk teknisi jika status menunggu) -->
                                                                @if (Auth::user()->role === 'teknisi' && $booking->status == 'menunggu')
                                                                    <button type="button" class="btn btn-success btn-sm"
                                                                        onclick="event.stopPropagation(); approveBooking({{ $booking->id ?? 'null' }})"
                                                                        title="Setujui Booking">
                                                                        <i class="bi bi-check-circle"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            @elseif($isOccupiedSlot)
                                                <!-- Slot ini sudah di-cover oleh colspan dari slot sebelumnya -->
                                                <!-- Tidak perlu render apa-apa karena sudah termasuk dalam colspan -->
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
            @endif
        @endforeach

        @if ($bookings->count() == 0)
            <div class="text-center py-5">
                <div class="alert alert-info">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <h4 class="mt-3">Tidak ada booking untuk tanggal {{ $selectedDate }}</h4>
                    <p class="mb-0">Semua ruangan tersedia untuk peminjaman.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL DETAIL - HANYA SATU DI SINI -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail booking...</p>
                    </div>
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
        // Pastikan Bootstrap tersedia
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap tidak ditemukan! Pastikan Bootstrap JS dimuat.');
        }

        // ==================== FUNGSI DETAIL BOOKING ====================
        window.showBookingDetail = function(bookingId) {
            if (!bookingId || bookingId === 'null') {
                alert('Booking ID tidak valid');
                return;
            }

            console.log('Menampilkan detail booking ID:', bookingId);

            // Tampilkan loading
            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat detail booking...</p>
                </div>
            `;

            // Tampilkan modal DULU sebelum fetch data
            const modalElement = document.getElementById('detailModal');
            if (!modalElement) {
                console.error('Modal #detailModal tidak ditemukan!');
                alert('Modal tidak ditemukan. Silakan refresh halaman.');
                return;
            }

            // Buat instance modal baru
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Tampilkan modal
            modal.show();

            // Fetch data booking
            fetch(`/bookings/${bookingId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Booking detail:', data);

                    // Format tanggal
                    const tanggal = new Date(data.tanggal).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    // Buat tombol aksi berdasarkan role
                    let actionButtons = '';
                    const isOwner = {{ Auth::check() ? 'true' : 'false' }} && data.user_id ==
                        {{ Auth::id() ?? 'null' }};
                    const isTeknisi = {{ Auth::check() && Auth::user()->role === 'teknisi' ? 'true' : 'false' }};

                    if (isOwner || isTeknisi) {
                        actionButtons += `
                            <button type="button" class="btn btn-primary me-2"
                                onclick="window.editBooking(${bookingId})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger me-2"
                                onclick="window.deleteBooking(${bookingId})">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        `;
                    }

                    if (isTeknisi && data.status === 'menunggu') {
                        actionButtons += `
                            <button type="button" class="btn btn-success"
                                onclick="window.approveBooking(${bookingId})">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        `;
                    }

                    // Format konten HTML
                    const content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">📋 Detail Peminjaman</h6>
                                <p><strong>👤 Peminjam:</strong> ${data.nama_peminjam || '-'}</p>
                                <p><strong>🎓 NIM:</strong> ${data.nim || '-'}</p>
                                <p><strong>📞 No. HP:</strong> ${data.no_hp || '-'}</p>
                                <p><strong>📧 Email:</strong> ${data.pemesan_email || data.email || '-'}</p>
                                <p><strong>👥 Jumlah Peserta:</strong> ${data.jumlah_peserta || '1'}</p>
                                <p><strong>📝 Keperluan:</strong> ${data.keperluan || '-'}</p>
                                <p><strong>🏷️ Status:</strong> 
                                    <span class="badge ${data.status == 'disetujui' ? 'bg-success' : data.status == 'menunggu' ? 'bg-warning' : 'bg-danger'}">
                                        ${data.status == 'disetujui' ? '✓ Disetujui' : data.status == 'menunggu' ? '⏳ Menunggu' : '✗ Ditolak'}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">🕒 Detail Waktu & Ruangan</h6>
                                <p><strong>📅 Tanggal:</strong> ${tanggal}</p>
                                <p><strong>⏰ Jam:</strong> ${data.jam_mulai || '-'} - ${data.jam_selesai || '-'}</p>
                                <p><strong>🚪 Ruangan:</strong> ${data.ruangan?.nama || '-'}</p>
                                <p><strong>🏢 Gedung:</strong> ${data.ruangan?.gedung || '-'}</p>
                                <p><strong>🏗️ Lantai:</strong> ${data.ruangan?.lantai || '-'}</p>
                                <p><strong>🧑‍🤝‍🧑 Kapasitas:</strong> ${data.ruangan?.kapasitas || '-'} orang</p>
                                <p><strong>🔧 Fasilitas:</strong> ${data.ruangan?.fasilitas ? (typeof data.ruangan.fasilitas === 'string' ? data.ruangan.fasilitas : JSON.stringify(data.ruangan.fasilitas)) : '-'}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-2">📝 Catatan Tambahan</h6>
                                <div class="p-3 bg-light rounded">
                                    ${data.catatan || '<em class="text-muted">Tidak ada catatan</em>'}
                                </div>
                            </div>
                        </div>
                        ${actionButtons ? `
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-12 text-center">
                                            ${actionButtons}
                                        </div>
                                    </div>
                                ` : ''}
                    `;

                    // Update konten modal
                    document.getElementById('detailContent').innerHTML = content;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Gagal memuat detail booking. Silakan coba lagi.<br>
                            <small>Error: ${error.message}</small>
                        </div>
                    `;
                });
        };

        // ==================== FUNGSI EDIT BOOKING ====================
        window.editBooking = function(bookingId) {
            if (!bookingId || bookingId === 'null') return;

            if (confirm('Apakah Anda ingin mengedit booking ini?')) {
                // Tutup modal dulu
                const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                if (modal) {
                    modal.hide();
                }
                // Redirect ke halaman edit
                window.location.href = `/bookings/${bookingId}/edit`;
            }
        };

        // ==================== FUNGSI HAPUS BOOKING ====================
        window.deleteBooking = function(bookingId) {
            if (!bookingId || bookingId === 'null') return;

            if (confirm('Apakah Anda yakin ingin menghapus booking ini?')) {
                // Kirim request DELETE
                fetch(`/bookings/${bookingId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Booking berhasil dihapus!');
                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                            if (modal) {
                                modal.hide();
                            }
                            // Refresh halaman
                            window.location.reload();
                        } else {
                            alert('Gagal menghapus booking: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus booking: ' + error.message);
                    });
            }
        };

        // ==================== FUNGSI APPROVE BOOKING ====================
        window.approveBooking = function(bookingId) {
            if (!bookingId || bookingId === 'null') return;

            if (confirm('Apakah Anda ingin menyetujui booking ini?')) {
                // Kirim request POST
                fetch(`/bookings/${bookingId}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Booking berhasil disetujui!');
                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                            if (modal) {
                                modal.hide();
                            }
                            // Refresh halaman
                            window.location.reload();
                        } else {
                            alert('Gagal menyetujui booking: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyetujui booking: ' + error.message);
                    });
            }
        };
    </script>
@endpush
