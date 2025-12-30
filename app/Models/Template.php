<?php

namespace App\Models;

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
    ];

    // protected $casts = [
    //     'share_to_bidang' => 'boolean',
    // ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Relasi ke Bidang
    public function bidangs()
    {
        return $this->belongsToMany(\App\Models\Bidang::class, 'template_bidang')
        ->withTimestamps();
    }

    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class, 'bidang_id');
    }
}
