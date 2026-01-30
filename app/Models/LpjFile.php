<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LpjFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lpj_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'lpj_id' => 'integer',
        'file_size' => 'integer',
    ];

    public function lpj()
    {
        return $this->belongsTo(Lpj::class);
    }
}
