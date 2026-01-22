<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(SavedFile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}