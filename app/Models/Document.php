<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bidang_id',
        'sie-id',
        'title',
        'filename',
        'path',
        'file_type',
        'description',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function sie()
    {
        return $this->belongsTo(Sie::class);
    }
}
