<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jajahan',
        'jawatan',
        'no_telefon',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPegawaiJajahan(): bool
    {
        return $this->role === 'pegawai_jajahan' || $this->role === 'admin';
    }

    public function isPegawaiNegeri(): bool
    {
        return $this->role === 'pegawai_negeri' || $this->role === 'admin';
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'admin' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Pentadbir</span>',
            'pegawai_negeri' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pegawai JPVNK (Negeri)</span>',
            'pegawai_jajahan' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Pegawai JPV Jajahan (' . ($this->jajahan ?? 'Umum') . ')</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pengguna</span>',
        };
    }
}
