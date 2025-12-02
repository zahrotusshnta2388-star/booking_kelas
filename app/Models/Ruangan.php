<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    // Hapus HasFactory jika error
    // use HasFactory;

    protected $fillable = [
        'nama', 'kode', 'gedung', 'lantai', 'kapasitas',
        'fasilitas', 'deskripsi', 'status'
    ];

    protected $casts = [
        'fasilitas' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking');
    }

    // Scope untuk ruangan tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    // Helper method
    public function getFasilitasListAttribute()
    {
        if (empty($this->fasilitas)) {
            return 'Tidak ada';
        }
        
        // Jika fasilitas adalah array
        if (is_array($this->fasilitas)) {
            return implode(', ', $this->fasilitas);
        }
        
        // Jika fasilitas adalah string JSON
        if (is_string($this->fasilitas)) {
            $fasilitasArray = json_decode($this->fasilitas, true);
            return $fasilitasArray ? implode(', ', $fasilitasArray) : 'Tidak ada';
        }
        
        return 'Tidak ada';
    }
    
    // Helper untuk cek status
    public function isTersedia()
    {
        return $this->status === 'tersedia';
    }
    
    // Helper untuk label status
    public function getStatusLabelAttribute()
    {
        return $this->status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia';
    }
    
    // Helper untuk badge status
    public function getStatusBadgeAttribute()
    {
        $class = $this->status === 'tersedia' ? 'badge bg-success' : 'badge bg-danger';
        return '<span class="' . $class . '">' . $this->status_label . '</span>';
    }
}