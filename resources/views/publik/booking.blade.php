<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .required:after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Form Pemesanan Ruangan</h4>
                    </div>
                    
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('booking.publik.store') }}" method="POST" id="bookingForm">
                            @csrf
                            
                            <!-- Data Peminjam -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2">Data Peminjam</h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nama_peminjam" class="form-label required">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_peminjam" name="nama_peminjam" 
                                           value="{{ old('nama_peminjam') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nim" class="form-label">NIM/NIP</label>
                                    <input type="text" class="form-control" id="nim" name="nim" 
                                           value="{{ old('nim') }}">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label required">No. HP/WhatsApp</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                           value="{{ old('no_hp') }}" required>
                                </div>
                            </div>
                            
                            <!-- Detail Pemesanan -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2">Detail Pemesanan</h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ruangan_id" class="form-label required">Ruangan</label>
                                    <select class="form-select" id="ruangan_id" name="ruangan_id" required>
                                        <option value="">Pilih Ruangan</option>
                                        @foreach($ruangans as $ruangan)
                                            <option value="{{ $ruangan->id }}" 
                                                {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                                {{ $ruangan->nama_ruangan }} (Kapasitas: {{ $ruangan->kapasitas }} orang)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                                    <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" 
                                           value="{{ old('jumlah_peserta') }}" min="1">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="tanggal" class="form-label required">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                           value="{{ old('tanggal') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="jam_mulai" class="form-label required">Jam Mulai</label>
                                    <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" 
                                           value="{{ old('jam_mulai') }}" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="jam_selesai" class="form-label required">Jam Selesai</label>
                                    <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" 
                                           value="{{ old('jam_selesai') }}" required>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="keperluan" class="form-label required">Keperluan/Kegiatan</label>
                                    <textarea class="form-control" id="keperluan" name="keperluan" 
                                              rows="3" required>{{ old('keperluan') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Persetujuan -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2">Persetujuan</h5>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="syarat_ketentuan" 
                                               name="syarat_ketentuan" value="1" 
                                               {{ old('syarat_ketentuan') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="syarat_ketentuan">
                                            Saya telah membaca dan menyetujui syarat & ketentuan peminjaman ruangan
                                        </label>
                                    </div>
                                    @error('syarat_ketentuan')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="konfirmasi_data" 
                                               name="konfirmasi_data" value="1" 
                                               {{ old('konfirmasi_data') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="konfirmasi_data">
                                            Saya menkonfirmasi bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan
                                        </label>
                                    </div>
                                    @error('konfirmasi_data')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Tombol Submit -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('booking.publik') }}" class="btn btn-secondary me-md-2">
                                            Reset Form
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Kirim Permohonan Booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer text-muted">
                        <small>
                            * Permohonan akan diproses dalam 1x24 jam.<br>
                            * Pastikan data yang diisi sudah benar.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set min date untuk input tanggal
        document.getElementById('tanggal').min = new Date().toISOString().split('T')[0];
        
        // Validasi jam
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;
            
            if (jamMulai && jamSelesai) {
                if (jamSelesai <= jamMulai) {
                    e.preventDefault();
                    alert('Jam selesai harus setelah jam mulai!');
                    return false;
                }
            }
        });
    </script>
</body>
</html>