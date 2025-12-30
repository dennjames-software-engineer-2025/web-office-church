<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Dom\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'team_type',
        'jabatan',
        'bidang_id',
        'sie_id',
        'alasan_ditolak',
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

    // Models User <One to Many> Bidang
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    // Models User <One to Many> Sie
    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function anggota()
    {
        return $this->hasOne(User::class);
    }

    public function getJabatanLabelAttribute(): string
    {
        if (! $this->jabatan) {
            return '-';
        }

        $map = [
            'super_admin'   => 'Super Admin',
            'ketua'         => 'Ketua',
            'wakil_ketua'   => 'Wakil Ketua',
            'sekretaris_1'  => 'Sekretaris 1',
            'sekretaris_2'  => 'Sekretaris 2',
            'bendahara_1'   => 'Bendahara 1',
            'bendahara_2'   => 'Bendahara 2',
            'ketua_bidang'  => 'Ketua Bidang',
            'anggota_sie'   => 'Anggota Sie',
        ];

        if (array_key_exists($this->jabatan, $map)) {
            return $map[$this->jabatan];
        }

        return Str::title(str_replace('_', ' ', $this->jabatan));
    }

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
}
