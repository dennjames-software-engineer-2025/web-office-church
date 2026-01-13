<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',

        // kolom tambahan (pastikan ada di migration users)
        'status',
        'jabatan',          // contoh: ketua, wakil_ketua, sekretaris_1, bendahara_2, ketua_bidang, ketua_sie, ketua_lingkungan, anggota_komunitas
        'team_type',        // kalau masih dipakai, kalau tidak nanti kita buang
        'bidang_id',
        'sie_id',
        'alasan_ditolak',
        'kedudukan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected static function booted()
    {
        static::deleting(function (User $user) {
            if (! $user->isForceDeleting()) {
                $user->email = $user->email . '__deleted__' . now()->timestamp . '__' . $user->id;
                $user->saveQuietly();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi struktur lama (kalau masih dipakai)
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }

    // Kalau kamu punya model Document, pastikan namespace-nya benar
    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class);
    }

    // Label jabatan untuk UI
    public function getJabatanLabelAttribute(): string
    {
        if (! $this->jabatan) return '-';

        $map = [
            'super_admin'        => 'Super Admin',

            'ketua'              => 'Ketua',
            'wakil_ketua'        => 'Wakil Ketua',

            'sekretaris_1'       => 'Sekretaris 1',
            'sekretaris_2'       => 'Sekretaris 2',

            'bendahara_1'        => 'Bendahara 1',
            'bendahara_2'        => 'Bendahara 2',

            'ketua_bidang'       => 'Ketua Bidang',
            'ketua_sie'          => 'Ketua Sie',

            'ketua_lingkungan'   => 'Ketua Lingkungan',
            'anggota_komunitas'  => 'Anggota Komunitas',
        ];

        return $map[$this->jabatan] ?? Str::title(str_replace('_', ' ', $this->jabatan));
    }
}
