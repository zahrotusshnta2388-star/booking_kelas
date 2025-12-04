{{-- resources/views/teknisi/bookings/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Booking</h2>

        <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="ruangan_id">Ruangan</label>
                <select name="ruangan_id" id="ruangan_id" class="form-control">
                    @foreach ($ruangans as $ruangan)
                        <option value="{{ $ruangan->id }}" {{ $booking->ruangan_id == $ruangan->id ? 'selected' : '' }}>
                            {{ $ruangan->nama }} (Kapasitas: {{ $ruangan->kapasitas }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $booking->tanggal }}"
                    required>
            </div>

            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="{{ $booking->jam_mulai }}"
                    required>
            </div>

            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control"
                    value="{{ $booking->jam_selesai }}" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control">{{ $booking->keterangan }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Booking</button>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
{{-- resources/views/teknisi/bookings/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Booking (Admin)</h2>

        <form action="{{ route('teknisi.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Semua field termasuk status -->
            <input type="text" name="nama_peminjam" value="{{ $booking->nama_peminjam }}">
            <input type="text" name="no_hp" value="{{ $booking->no_hp }}">

            <!-- FIELD STATUS hanya untuk teknisi -->
            <select name="status" class="form-control">
                <option value="menunggu" {{ $booking->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ $booking->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ $booking->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
