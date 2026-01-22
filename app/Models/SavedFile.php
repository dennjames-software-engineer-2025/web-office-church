<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'source_type',
        'source_id',
        'title_override',
        'is_private',
        'shared_roles',
        'shared_bidang_ids',
        'added_by',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'shared_roles' => 'array',
        'shared_bidang_ids' => 'array',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function source()
    {
        return $this->morphTo(null, 'source_type', 'source_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
