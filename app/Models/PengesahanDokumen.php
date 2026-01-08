<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengesahanDokumen extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'tujuan',
        'status',
        'alasan_ditolak',
        'approved_by',
        'approved_at',
        'surat_path',
        'watermarked_path',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengesah()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}