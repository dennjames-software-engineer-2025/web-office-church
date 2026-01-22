<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bidang_id',
        'sie_id',
        'title',
        'filename',
        'path',
        'file_type',
        'description',
        'shared_bidang_ids',
    ];

    protected $casts = [
        'shared_bidang_ids' => 'array',
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

    public function sharedBidangs()
    {
        return $this->belongsToMany(Bidang::class, 'document_bidang')
            ->withTimestamps();
    }
}
