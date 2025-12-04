@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-calendar-plus me-2"></i>Buat Booking Massal
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('teknisi.bookings.store-bulk') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nama_peminjam" class="form-label">Nama Peminjam *</label>
                                <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                    id="nama_peminjam" name="nama_peminjam" value="{{ old('nama_peminjam') }}" required>
                                @error('nama_peminjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nim" class="form-label">NIM</label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror"
                                        id="nim" name="nim" value="{{ old('nim') }}">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">No. HP <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                                        id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ruangan_id" class="form-label">Ruangan *</label>
                                <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id"
                                    name="ruangan_id" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}"
                                            {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama }} - Lantai {{ $ruangan->lantai }}
                                            (Kapasitas: {{ $ruangan->kapasitas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai *</label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                        id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                        required>
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai *</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                        id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                        required>
                                    @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai *</label>
                                    <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror"
                                        id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai *</label>
                                    <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror"
                                        id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}"
                                        required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hari yang Dipesan *</label>
                                <div class="border rounded p-3">
                                    <div class="row">
                                        @php
                                            $days = [
                                                'senin' => 'Senin',
                                                'selasa' => 'Selasa',
                                                'rabu' => 'Rabu',
                                                'kamis' => 'Kamis',
                                                'jumat' => 'Jumat',
                                                'sabtu' => 'Sabtu',
                                                'minggu' => 'Minggu',
                                            ];
                                            $oldDays = old('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
                                        @endphp
                                        @foreach ($days as $key => $label)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari[]"
                                                        value="{{ $key }}" id="hari_{{ $key }}"
                                                        {{ in_array($key, $oldDays) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="hari_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('hari')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                                <input type="number" class="form-control @error('jumlah_peserta') is-invalid @enderror"
                                    id="jumlah_peserta" name="jumlah_peserta" min="1"
                                    value="{{ old('jumlah_peserta', 25) }}">
                                @error('jumlah_peserta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Keperluan *</label>
                                <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan"
                                    rows="3" required>{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Informasi:</strong> Booking akan dibuat untuk setiap hari yang dipilih
                                dalam rentang tanggal yang ditentukan. Semua booking akan langsung disetujui.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('teknisi.dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus me-1"></i> Buat Booking Massal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi tanggal
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');

            tanggalMulai.addEventListener('change', function() {
                tanggalSelesai.min = this.value;
                if (tanggalSelesai.value && tanggalSelesai.value < this.value) {
                    tanggalSelesai.value = this.value;
                }
            });

            // Validasi jam
            const jamMulai = document.getElementById('jam_mulai');
            const jamSelesai = document.getElementById('jam_selesai');

            jamSelesai.addEventListener('change', function() {
                if (jamMulai.value && this.value <= jamMulai.value) {
                    alert('Jam selesai harus setelah jam mulai!');
                    this.value = '';
                }
            });

            // Set min date ke hari ini
            const today = new Date().toISOString().split('T')[0];
            tanggalMulai.min = today;
            tanggalSelesai.min = today;
        });
    </script>
@endsection
