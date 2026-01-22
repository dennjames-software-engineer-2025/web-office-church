<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'body',
        'status',
        'is_pinned',
        'starts_at',
        'ends_at',
        'target_kedudukan',
        'target_roles',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'target_kedudukan' => 'array',
        'target_roles' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActiveNow(): bool
    {
        $now = now();

        if ($this->status !== 'published') return false;

        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }
}
