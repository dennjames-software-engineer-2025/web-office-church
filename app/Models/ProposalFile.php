<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /* File hanya milik satu proposal (Spesifikasi dari file) */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}