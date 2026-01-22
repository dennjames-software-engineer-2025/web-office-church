<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'bidang_id',
        'sie_id',

        'judul',
        'tujuan',

        'status',
        'stage',

        'dpp_harian_until',
        'notes',

        'ketua_bidang_approved_by',
        'ketua_bidang_approved_at',

        'romo_approved_by',
        'romo_approved_at',

        'proposal_no',
        'receipt_path',

        // reject info (kamu sudah punya)
        'reject_reason',
        'rejected_by',
        'rejected_at',
        'rejected_stage',
    ];

    protected $casts = [
        'dpp_harian_until'          => 'datetime',
        'ketua_bidang_approved_at'  => 'datetime',
        'romo_approved_at'          => 'datetime',
        'rejected_at'               => 'datetime',
    ];

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }

    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }
}