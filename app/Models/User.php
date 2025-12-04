<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- TAMBAHKAN INI
        'nim',  // <-- TAMBAHKAN INI (optional)
        'no_hp', // <-- TAMBAHKAN INI (optional)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // RELASI KE BOOKINGS
    public function bookings()
    {
        return $this->hasMany(Booking::class); // <-- TAMBAHKAN INI
    }

    // METHOD UNTUK CEK ROLE
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeknisi()
    {
        return $this->role === 'teknisi';
    }

    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa' || !$this->role;
    }

    // ACCESSOR UNTUK ROLE LABEL
    public function getRoleLabelAttribute()
    {
        $roles = [
            'admin' => 'Admin',
            'teknisi' => 'Teknisi',
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'staff' => 'Staff',
        ];

        return $roles[$this->role] ?? 'Pengguna';
    }

    // ACCESSOR UNTUK ROLE BADGE
    public function getRoleBadgeAttribute()
    {
        $badgeClasses = [
            'admin' => 'bg-danger',
            'teknisi' => 'bg-primary',
            'mahasiswa' => 'bg-success',
            'dosen' => 'bg-info',
            'staff' => 'bg-warning',
        ];

        $class = $badgeClasses[$this->role] ?? 'bg-secondary';

        return '<span class="badge ' . $class . '">' . $this->role_label . '</span>';
    }
}
