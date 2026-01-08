<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id',
        'judul',
        'tujuan',
        'status',
        'alasan_ditolak',
        'approved_by',
        'approved_at',
    ];

    /* Proposal milik satu program */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /* User pengaju proposal (Ketua Bidang / Anggota Sie) */
    public function pengaju()
    {
        return $this->belongsTo(User::class);
    }

    /* User yang menyetujui / menolak proposal */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* Proposal memiliki banyak file PDF */
    public function files()
    {
        return $this->hasMany(Proposal::class);
    }
}
