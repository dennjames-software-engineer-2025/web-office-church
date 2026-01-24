<?php

namespace App\Models;

use App\Models\Bidang;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'original_name',
        'path',
        'mime_type',
        'file_type',
        'size',
        'uploaded_by',
        'bidang_id',
        'share_to_bidang',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    // NOTE: relasi pivot ini boleh kamu HAPUS kalau memang sudah tidak dipakai lagi.
    public function bidangs()
    {
        return $this->belongsToMany(
            Bidang::class,
            'template_bidang',
            'template_id',
            'bidang_id'
        )->withTimestamps();
    }
}