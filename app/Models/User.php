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

        'status',
        'jabatan',
        'jabatan_key',          // ✅ wajib
        'team_type',
        'bidang_id',
        'sie_id',
        'kedudukan',
        'alasan_ditolak',
        'alasan_dihapus',

        // ✅ khusus Lingkungan (brief baru)
        'lingkungan_scope',     // wilayah|lingkungan
        'wilayah',              // wilayah_1..wilayah_7
        'lingkungan',           // nama lingkungan
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
            'password'          => 'hashed',
            'bidang_id'         => 'integer',
            'sie_id'            => 'integer',
        ];
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class);
    }

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
            'wakil_ketua_lingkungan' => 'Wakil Ketua Lingkungan',

            'anggota_komunitas'  => 'Anggota Komunitas',

            'sekretariat'        => 'Sekretariat',
        ];

        return $map[$this->jabatan] ?? Str::title(str_replace('_', ' ', $this->jabatan));
    }

    /**
     * DPP Harian = jabatan DPP inti (ketua/wakil/sekretaris/bendahara).
     */
    public function isDppHarian(): bool
    {
        return in_array($this->jabatan, [
            'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
        ], true);
    }

    public function canManageMinutes(): bool
    {
        if ($this->hasRole('super_admin')) return true;

        // kamu pakai role "sekretaris" untuk sekretaris_1 & sekretaris_2
        if (! $this->hasRole('sekretaris')) return false;

        return in_array($this->jabatan, ['sekretaris_1', 'sekretaris_2'], true);
    }

    public function canManageAnnouncements(): bool
    {
        return $this->hasRole('sekretaris') || $this->hasRole('super_admin');
    }
}
