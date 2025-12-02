@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-calendar-plus"></i> Ajukan Peminjaman Ruangan</h1>
        <a href="{{ route('ruangan.publik') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Ruangan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
            <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Form Pengajuan Peminjaman</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('booking.publik.store') }}" method="POST" id="bookingForm">
                @csrf
                
                <!-- BAGIAN 1: PILIH RUANGAN & WAKTU -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-clock"></i> Jadwal Peminjaman</h6>
                    </div>
                    <div class="card-body">
                        <!-- TOMBOL & FIELD UNTUK CEK KETERSEDIAAN -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="ruangan_id" class="form-label">Ruangan *</label>
                                <select class="form-select @error('ruangan_id') is-invalid @enderror" 
                                        id="ruangan_id" name="ruangan_id" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" 
                                            {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama }} ({{ $ruangan->kode }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih ruangan yang ingin dipinjam</small>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="tanggal" class="form-label">Tanggal *</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                       id="tanggal" name="tanggal" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       value="{{ old('tanggal') }}" 
                                       required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal H+1 dari hari ini</small>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai *</label>
                                <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                       id="jam_mulai" name="jam_mulai" 
                                       value="{{ old('jam_mulai', '08:00') }}" 
                                       required>
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai *</label>
                                <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       id="jam_selesai" name="jam_selesai" 
                                       value="{{ old('jam_selesai', '10:00') }}" 
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
                                       id="nama_peminjam" name="nama_peminjam" 
                                       value="{{ old('nama_peminjam') }}" 
                                       placeholder="Masukkan nama lengkap" 
                                       required>
                                @error('nama_peminjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nim" class="form-label">NIM/NIDN</label>
                                <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                                       id="nim" name="nim" 
                                       value="{{ old('nim') }}" 
                                       placeholder="Masukkan NIM (mahasiswa) atau NIDN (dosen)">
                                @error('nim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional, untuk keperluan verifikasi</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="nama@email.com" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Untuk konfirmasi booking</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No. HP/WhatsApp *</label>
                                <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" 
                                       id="no_hp" name="no_hp" 
                                       value="{{ old('no_hp') }}" 
                                       placeholder="0812-3456-7890" 
                                       required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan *</label>
                            <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                                      id="keperluan" name="keperluan" 
                                      rows="3" 
                                      placeholder="Jelaskan secara detail keperluan peminjaman ruangan" 
                                      required>{{ old('keperluan') }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: Kuliah Web Programming, Rapat Prodi, Praktikum Jaringan, dll.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                            <input type="number" class="form-control @error('jumlah_peserta') is-invalid @enderror" 
                                   id="jumlah_peserta" name="jumlah_peserta" 
                                   value="{{ old('jumlah_peserta', 1) }}" 
                                   min="1" 
                                   placeholder="Jumlah orang yang akan hadir">
                            @error('jumlah_peserta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Untuk penyesuaian dengan kapasitas ruangan</small>
                        </div>
                    </div>
                </div>
                
                <!-- BAGIAN 3: KONFIRMASI -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-check-circle"></i> Konfirmasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('syarat_ketentuan') is-invalid @enderror" 
                                   type="checkbox" 
                                   id="syarat_ketentuan" 
                                   name="syarat_ketentuan" 
                                   required>
                            <label class="form-check-label" for="syarat_ketentuan">
                                Saya menyetujui Syarat & Ketentuan Peminjaman Ruangan
                            </label>
                            @error('syarat_ketentuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input @error('konfirmasi_data') is-invalid @enderror" 
                                   type="checkbox" 
                                   id="konfirmasi_data" 
                                   name="konfirmasi_data" 
                                   required>
                            <label class="form-check-label" for="konfirmasi_data">
                                Saya telah memeriksa dan memastikan data yang diisi sudah benar
                            </label>
                            @error('konfirmasi_data')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-info-circle"></i> Proses Verifikasi:</h6>
                            <p class="mb-0">
                                Pengajuan Anda akan diverifikasi oleh teknisi dalam waktu 1x24 jam. 
                                Status booking dapat dicek melalui email konfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- TOMBOL SUBMIT -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('jadwal.publik') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-calendar-week"></i> Lihat Jadwal
                        </a>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset Form
                        </button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-send"></i> Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- SYARAT & KETENTUAN -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Syarat & Ketentuan</h5>
        </div>
        <div class="card-body">
            <ol class="mb-0">
                <li>Peminjaman ruangan minimal diajukan <strong>H+1</strong> dari tanggal pengajuan</li>
                <li>Durasi peminjaman maksimal <strong>8 jam per hari</strong></li>
                <li>Peminjam bertanggung jawab atas kebersihan dan keutuhan fasilitas ruangan</li>
                <li>Booking dapat dibatalkan maksimal <strong>1 hari</strong> sebelum waktu peminjaman</li>
                <li>Teknisi berhak membatalkan booking jika ditemukan pelanggaran</li>
                <li>Harap datang tepat waktu sesuai jadwal yang telah disetujui</li>
                <li>Wajib melaporkan kerusakan fasilitas ruangan kepada teknisi</li>
            </ol>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkBtn = document.getElementById('checkAvailabilityBtn');
    const submitBtn = document.getElementById('submitBtn');
    const syaratCheck = document.getElementById('syarat_ketentuan');
    const konfirmasiCheck = document.getElementById('konfirmasi_data');
    
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
        
        // Validasi tanggal (minimal H+1)
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const selectedDate = new Date(tanggal);
        
        if (selectedDate < tomorrow) {
            showResult('Tanggal peminjaman minimal H+1 dari hari ini!', 'danger');
            return;
        }
        
        // Validasi jam
        if (jamMulai >= jamSelesai) {
            showResult('Jam selesai harus setelah jam mulai!', 'danger');
            return;
        }
        
        // Hitung durasi
        const startTime = new Date('1970-01-01T' + jamMulai + 'Z');
        const endTime = new Date('1970-01-01T' + jamSelesai + 'Z');
        const durationHours = (endTime - startTime) / (1000 * 60 * 60);
        
        if (durationHours > 8) {
            showResult('Durasi peminjaman maksimal 8 jam!', 'danger');
            return;
        }
        
        // Tampilkan loading
        showResult('<div class="spinner-border spinner-border-sm" role="status"></div> Memeriksa ketersediaan ruangan...', 'info');
        
        // Kirim request AJAX
        fetch('{{ route("ruangan.checkAvailability") }}', {
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
                showResult('✅ <strong>' + data.message + '</strong>. Ruangan tersedia untuk dipesan.', 'success');
            } else {
                showResult('❌ <strong>' + data.message + '</strong>. Silakan pilih waktu atau ruangan lain.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult('Terjadi kesalahan saat mengecek ketersediaan. Silakan coba lagi.', 'danger');
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
        }[type] || 'alert-info';
        
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
                    alert.remove();
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
            if (!syaratCheck.checked || !konfirmasiCheck.checked) {
                e.preventDefault();
                showResult('Harap setujui syarat & ketentuan dan konfirmasi data!', 'warning');
                
                // Scroll ke checkbox
                if (!syaratCheck.checked) {
                    syaratCheck.scrollIntoView({ behavior: 'smooth' });
                    syaratCheck.focus();
                } else if (!konfirmasiCheck.checked) {
                    konfirmasiCheck.scrollIntoView({ behavior: 'smooth' });
                    konfirmasiCheck.focus();
                }
            }
        });
    }
    
    // Set tanggal minimum ke besok
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('tanggal').setAttribute('min', minDate);
    
    // Auto-set tanggal ke besok jika belum diisi
    if (!document.getElementById('tanggal').value) {
        document.getElementById('tanggal').value = minDate;
    }
    
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
    
    // Validasi jumlah peserta dengan kapasitas ruangan
    document.getElementById('ruangan_id').addEventListener('change', function() {
        const ruanganId = this.value;
        if (ruanganId) {
            // Ambil kapasitas ruangan (bisa diimplementasikan dengan AJAX jika perlu)
            // Untuk sekarang, kita set max 50
            document.getElementById('jumlah_peserta').setAttribute('max', 50);
        }
    });
});
</script>
@endpush
@endsection