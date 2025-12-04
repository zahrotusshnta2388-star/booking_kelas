@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-calendar-plus"></i> Tambah Booking Baru</h1>
            <a href="" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Form Pemesanan Ruangan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('teknisi.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <!-- BAGIAN 1: PILIH RUANGAN & WAKTU -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-clock"></i> Jadwal Pemesanan</h6>
                        </div>
                        <div class="card-body">
                            <!-- TOMBOL & FIELD UNTUK CEK KETERSEDIAAN -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="ruangan_id" class="form-label">Ruangan *</label>
                                    <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id"
                                        name="ruangan_id" required>
                                        <option value="">Pilih Ruangan</option>
                                        @foreach ($ruangans as $ruangan)
                                            <!-- BENAR: ADA "as" -->
                                            <option value="{{ $ruangan->id }}"
                                                {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                                {{ $ruangan->nama }} ({{ $ruangan->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ruangan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-3 mb-3">
                                    <label for="tanggal" class="form-label">Tanggal *</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                        id="tanggal" name="tanggal" min="{{ date('Y-m-d') }}"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai *</label>
                                    <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror"
                                        id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai *</label>
                                    <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror"
                                        id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}"
                                        required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" id="checkAvailabilityBtn" class="btn btn-info w-100">
                                        <i class="bi bi-search"></i> Cek Ketersediaan
                                    </button>
                                </div>
                            </div>

                            <!-- AREA UNTUK HASIL CEK -->
                            <div id="availabilityResult" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: DATA PEMESAN -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-person"></i> Data Peminjam</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_peminjam" class="form-label">Nama Peminjam *</label>
                                    <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                                        id="nama_peminjam" name="nama_peminjam" value="{{ old('nama_peminjam') }}"
                                        placeholder="Masukkan nama lengkap" required>
                                    @error('nama_peminjam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nim" class="form-label">NIM</label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror"
                                        id="nim" name="nim" value="{{ old('nim') }}"
                                        placeholder="Masukkan NIM (jika mahasiswa)">
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keperluan" class="form-label">Keperluan *</label>
                                <input type="text" class="form-control @error('keperluan') is-invalid @enderror"
                                    id="keperluan" name="keperluan" value="{{ old('keperluan') }}"
                                    placeholder="Contoh: Kuliah, Rapat, Praktikum, Seminar" required>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. HP/WhatsApp *</label>
                                <input type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                                    id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="0812-3456-7890" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 3: STATUS (Hanya untuk teknisi) -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bi bi-gear"></i> Pengaturan</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Booking *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="menunggu" {{ old('status') == 'menunggu' ? 'selected' : '' }}>
                                        Menunggu Konfirmasi
                                    </option>
                                    <option value="disetujui" {{ old('status') == 'disetujui' ? 'selected' : '' }}>
                                        Disetujui
                                    </option>
                                    <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih status booking. Untuk publik otomatis "Menunggu
                                    Konfirmasi"</small>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL SUBMIT -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                            <label class="form-check-label" for="confirmCheck">
                                Saya telah memeriksa ketersediaan dan data sudah benar
                            </label>
                        </div>

                        <div>
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Simpan Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- INFO -->
        <div class="alert alert-info mt-4">
            <h6><i class="bi bi-info-circle"></i> Informasi Penting:</h6>
            <ul class="mb-0">
                <li>Pastikan ruangan tersedia sebelum melakukan booking</li>
                <li>Booking minimal 1 hari sebelum tanggal peminjaman</li>
                <li>Durasi peminjaman maksimal 8 jam per hari</li>
                <li>Booking akan diverifikasi oleh teknisi</li>
            </ul>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkBtn = document.getElementById('checkAvailabilityBtn');
                const submitBtn = document.getElementById('submitBtn');
                const confirmCheck = document.getElementById('confirmCheck');

                // Fungsi untuk mengecek ketersediaan
                function checkAvailability() {
                    const ruanganId = document.getElementById('ruangan_id').value;
                    const tanggal = document.getElementById('tanggal').value;
                    const jamMulai = document.getElementById('jam_mulai').value;
                    const jamSelesai = document.getElementById('jam_selesai').value;

                    // Validasi input
                    if (!ruanganId || !tanggal || !jamMulai || !jamSelesai) {
                        showResult('Harap lengkapi semua field terlebih dahulu!', 'warning');
                        return;
                    }

                    // Validasi jam
                    if (jamMulai >= jamSelesai) {
                        showResult('Jam selesai harus setelah jam mulai!', 'danger');
                        return;
                    }

                    // Tampilkan loading
                    showResult(
                        '<div class="spinner-border spinner-border-sm" role="status"></div> Memeriksa ketersediaan...',
                        'info');

                    // Kirim request AJAX
                    fetch('{{ route('ruangan.checkAvailability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ruangan_id: ruanganId,
                                tanggal: tanggal,
                                jam_mulai: jamMulai,
                                jam_selesai: jamSelesai
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.available) {
                                showResult('✅ <strong>' + data.message +
                                    '</strong>. Ruangan tersedia untuk dipesan.', 'success');
                            } else {
                                showResult('❌ <strong>' + data.message + '</strong>. Silakan pilih waktu lain.',
                                    'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showResult('Terjadi kesalahan saat mengecek ketersediaan. Silakan coba lagi.',
                                'danger');
                        });
                }

                // Fungsi untuk menampilkan hasil
                function showResult(message, type) {
                    const resultDiv = document.getElementById('availabilityResult');
                    const alertClass = {
                        'success': 'alert-success',
                        'danger': 'alert-danger',
                        'warning': 'alert-warning',
                        'info': 'alert-info'
                    } [type] || 'alert-info';

                    resultDiv.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

                    // Auto close setelah 5 detik (kecuali warning/error)
                    if (type !== 'danger' && type !== 'warning') {
                        setTimeout(() => {
                            const alert = resultDiv.querySelector('.alert');
                            if (alert) {
                                const bsAlert = new bootstrap.Alert(alert);
                                bsAlert.close();
                            }
                        }, 5000);
                    }
                }

                // Event listener untuk tombol cek ketersediaan
                if (checkBtn) {
                    checkBtn.addEventListener('click', checkAvailability);
                }

                // AUTO CHECK KETIKA FIELD BERUBAH
                ['ruangan_id', 'tanggal', 'jam_mulai', 'jam_selesai'].forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.addEventListener('change', function() {
                            // Jika semua field sudah diisi, auto check
                            const ruanganId = document.getElementById('ruangan_id').value;
                            const tanggal = document.getElementById('tanggal').value;
                            const jamMulai = document.getElementById('jam_mulai').value;
                            const jamSelesai = document.getElementById('jam_selesai').value;

                            if (ruanganId && tanggal && jamMulai && jamSelesai) {
                                // Delay sedikit untuk menghindari spam request
                                setTimeout(checkAvailability, 500);
                            }
                        });
                    }
                });

                // Validasi form sebelum submit
                if (submitBtn) {
                    document.getElementById('bookingForm').addEventListener('submit', function(e) {
                        if (!confirmCheck.checked) {
                            e.preventDefault();
                            showResult('Harap centang konfirmasi bahwa data sudah benar!', 'warning');

                            // Scroll ke checkbox
                            confirmCheck.scrollIntoView({
                                behavior: 'smooth'
                            });
                            confirmCheck.focus();
                        }
                    });
                }

                // Set tanggal minimum ke hari ini
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('tanggal').setAttribute('min', today);

                // Auto-set jam selesai berdasarkan jam mulai
                document.getElementById('jam_mulai').addEventListener('change', function() {
                    const jamMulai = this.value;
                    if (jamMulai) {
                        const [hours, minutes] = jamMulai.split(':');
                        const endHours = (parseInt(hours) + 2) % 24;
                        const jamSelesai = document.getElementById('jam_selesai');
                        jamSelesai.value = endHours.toString().padStart(2, '0') + ':' + minutes;
                    }
                });
            });
        </script>
    @endpush
@endsection
