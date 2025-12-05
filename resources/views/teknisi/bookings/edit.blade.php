{{-- resources/views/teknisi/bookings/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Edit Booking (Teknisi)</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('teknisi.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_peminjam" class="form-label">Nama Peminjam *</label>
                    <input type="text" class="form-control" id="nama_peminjam" name="nama_peminjam"
                        value="{{ old('nama_peminjam', $booking->nama_peminjam) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input type="text" class="form-control" id="nim" name="nim"
                        value="{{ old('nim', $booking->nim) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No. HP *</label>
                    <input type="tel" class="form-control" id="no_hp" name="no_hp"
                        value="{{ old('no_hp', $booking->no_hp) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="pemesan_email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="pemesan_email" name="pemesan_email"
                        value="{{ old('pemesan_email', $booking->pemesan_email) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="ruangan_id" class="form-label">Ruangan *</label>
                <select class="form-select" id="ruangan_id" name="ruangan_id" required>
                    <option value="">Pilih Ruangan</option>
                    @foreach ($ruangans as $ruangan)
                        <option value="{{ $ruangan->id }}"
                            {{ old('ruangan_id', $booking->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                            {{ $ruangan->nama }} - Lantai {{ $ruangan->lantai }} (Kapasitas: {{ $ruangan->kapasitas }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal *</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                        value="{{ old('tanggal', \Carbon\Carbon::parse($booking->tanggal)->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="jam_mulai" class="form-label">Jam Mulai *</label>
                    <input type="time" class="form-control" id="jam_mulai" name="jam_mulai"
                        value="{{ old('jam_mulai', \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i')) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="jam_selesai" class="form-label">Jam Selesai *</label>
                    <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                        value="{{ old('jam_selesai', \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i')) }}"
                        required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jumlah_peserta" class="form-label">Jumlah Peserta *</label>
                    <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" min="1"
                        value="{{ old('jumlah_peserta', $booking->jumlah_peserta) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="menunggu" {{ old('status', $booking->status) == 'menunggu' ? 'selected' : '' }}>
                            Menunggu</option>
                        <option value="disetujui" {{ old('status', $booking->status) == 'disetujui' ? 'selected' : '' }}>
                            Disetujui</option>
                        <option value="ditolak" {{ old('status', $booking->status) == 'ditolak' ? 'selected' : '' }}>
                            Ditolak</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="keperluan" class="form-label">Keperluan *</label>
                <textarea class="form-control" id="keperluan" name="keperluan" rows="3" required>{{ old('keperluan', $booking->keperluan) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="2">{{ old('catatan', $booking->catatan) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Update Booking
                </button>
                <a href="{{ route('teknisi.bookings.index') }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        // Validasi jam
        document.getElementById('jam_selesai').addEventListener('change', function() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = this.value;

            if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
                alert('Jam selesai harus setelah jam mulai!');
                this.value = '';
            }
        });
    </script>
@endsection
