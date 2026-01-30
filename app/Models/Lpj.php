<?php

namespace App\Models;

use App\Models\Sie;
use App\Models\User;
use App\Models\Bidang;
use App\Models\LpjFile;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lpj extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proposal_id',
        'created_by',
        'bidang_id',
        'sie_id',
        'status',
        'stage',
        'notes',
        'reject_reason',
        'rejected_by',
        'rejected_at',
        'rejected_stage',
        'ketua_bidang_approved_by',
        'ketua_bidang_approved_at',
        'final_approved_by',
        'final_approved_at',
        'lpj_name',
        'lpj_date',
    ];

    protected $casts = [
        'proposal_id'               => 'integer',
        'created_by'                => 'integer',
        'bidang_id'                 => 'integer',
        'sie_id'                    => 'integer',
        'rejected_at'               => 'datetime',
        'ketua_bidang_approved_at'  => 'datetime',
        'final_approved_at'         => 'datetime',
        'lpj_date'                  => 'date'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(LpjFile::class);
    }
}
