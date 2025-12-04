{{-- resources/views/publik/booking-edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Booking</h4>
                        <small class="text-white">Booking ID: #{{ $booking->id }}</small>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>Mohon periksa kesalahan berikut:
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_peminjam" class="form-label fw-bold">Nama Peminjam <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                        id="nama_peminjam" name="nama_peminjam"
                                        value="{{ old('nama_peminjam', $booking->nama_peminjam) }}" required>
                                    @error('nama_peminjam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nim" class="form-label fw-bold">NIM</label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror"
                                        id="nim" name="nim" value="{{ old('nim', $booking->nim) }}">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label fw-bold">No. HP <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                                        id="no_hp" name="no_hp" value="{{ old('no_hp', $booking->no_hp) }}" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pemesan_email" class="form-label fw-bold">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('pemesan_email') is-invalid @enderror"
                                        id="pemesan_email" name="pemesan_email"
                                        value="{{ old('pemesan_email', $booking->pemesan_email) }}" required>
                                    @error('pemesan_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ruangan_id" class="form-label fw-bold">Ruangan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id"
                                    name="ruangan_id" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}"
                                            {{ old('ruangan_id', $booking->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama }} - Lantai {{ $ruangan->lantai }}
                                            (Kapasitas: {{ $ruangan->kapasitas }}, {{ $ruangan->status }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label fw-bold">Tanggal <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                        id="tanggal" name="tanggal" min="{{ date('Y-m-d') }}"
                                        value="{{ old('tanggal', $booking->tanggal) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="jam_mulai" class="form-label fw-bold">Jam Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror"
                                        id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', $booking->jam_mulai) }}"
                                        required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="jam_selesai" class="form-label fw-bold">Jam Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror"
                                        id="jam_selesai" name="jam_selesai"
                                        value="{{ old('jam_selesai', $booking->jam_selesai) }}" required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_peserta" class="form-label fw-bold">Jumlah Peserta <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('jumlah_peserta') is-invalid @enderror"
                                        id="jumlah_peserta" name="jumlah_peserta" min="1"
                                        value="{{ old('jumlah_peserta', $booking->jumlah_peserta) }}" required>
                                    @error('jumlah_peserta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status Booking</label>
                                    <div class="form-control bg-light">
                                        @if ($booking->status == 'menunggu')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i> Menunggu Konfirmasi
                                            </span>
                                        @elseif($booking->status == 'disetujui')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Disetujui
                                            </span>
                                        @elseif($booking->status == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                        <small class="d-block mt-1">Status hanya dapat diubah oleh teknisi</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label fw-bold">Keperluan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan"
                                    rows="3" required>{{ old('keperluan', $booking->keperluan) }}</textarea>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label fw-bold">Catatan Tambahan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="2">{{ old('catatan', $booking->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="card bg-light border-0 p-3 mb-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h6>
                                <ul class="mb-0 small">
                                    <li>Pastikan data yang Anda isi sudah benar</li>
                                    <li>Booking akan dikonfirmasi oleh teknisi dalam waktu 1x24 jam</li>
                                    <li>Hubungi teknisi jika ada perubahan mendesak</li>
                                    <li>Ruangan hanya bisa dipesan maksimal 30 hari ke depan</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('booking.my') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>

                                <div>
                                    <button type="reset" class="btn btn-outline-danger me-2">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Booking
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i> Dibuat:
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-history me-1"></i> Terakhir diubah:
                                    {{ \Carbon\Carbon::parse($booking->updated_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validasi client-side untuk jam
        document.getElementById('jam_selesai').addEventListener('change', function() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = this.value;

            if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
                alert('Jam selesai harus setelah jam mulai!');
                this.value = '';
            }
        });

        // Validasi tanggal minimal hari ini
        document.getElementById('tanggal').min = new Date().toISOString().split('T')[0];
    </script>
@endsection
