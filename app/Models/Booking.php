<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruangan_id',
        'user_id', // TAMBAHKAN INI
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'nama_peminjam',
        'nim',
        'keperluan',
        'no_hp',
        'status',
        'jumlah_peserta',
        'pemesan_email',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class); // <-- TAMBAHKAN INI
    }

    // RELASI KE RUANGAN
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    // Scope untuk booking aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'disetujui');
    }

    // Scope untuk menunggu konfirmasi
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    // Scope untuk ditolak
    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Helper method untuk status
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'menunggu' => 'Menunggu Konfirmasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak'
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    // Helper method untuk badge warna status
    public function getStatusBadgeAttribute()
    {
        $badgeClasses = [
            'menunggu' => 'bg-warning',
            'disetujui' => 'bg-success',
            'ditolak' => 'bg-danger'
        ];

        $class = $badgeClasses[$this->status] ?? 'bg-secondary';

        return '<span class="badge ' . $class . '">' . $this->status_label . '</span>';
    }

    // Accessor untuk nama pemesan (backward compatibility)
    public function getPemesanNamaAttribute($value)
    {
        return $value ?? $this->nama_peminjam;
    }

    public function setPemesanNamaAttribute($value)
    {
        $this->attributes['pemesan_nama'] = $value;
        $this->attributes['nama_peminjam'] = $value;
    }

    // Accessor untuk no hp (backward compatibility)
    public function getPemesanNoHpAttribute($value)
    {
        return $value ?? $this->no_hp;
    }

    public function setPemesanNoHpAttribute($value)
    {
        $this->attributes['pemesan_no_hp'] = $value;
        $this->attributes['no_hp'] = $value;
    }

    // Accessor untuk kegiatan (backward compatibility)
    public function getKegiatanAttribute($value)
    {
        return $value ?? $this->keperluan;
    }

    public function setKegiatanAttribute($value)
    {
        $this->attributes['kegiatan'] = $value;
        $this->attributes['keperluan'] = $value;
    }
}
