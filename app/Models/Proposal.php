<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'created_by',
        'judul',
        'tujuan',
        'status',
        'stage',

        'ketua_approved_by',
        'ketua_approved_at',
        'bendahara1_approved_by',
        'bendahara1_approved_at',
        'bendahara2_approved_by',
        'bendahara2_approved_at',

        'reject_reason',
        'rejected_by',
        'rejected_at',
        'rejected_stage',
    ];

    protected $casts = [
        'ketua_approved_at'         => 'datetime',
        'bendahara1_approved_at'    => 'datetime',
        'bendahara2_approved_at'    => 'datetime',
        'rejected_at'               => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }
}
