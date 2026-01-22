<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinute extends Model
{
    protected $fillable = [
        'title','meeting_at','location','agenda','content',
        'status','kedudukan','created_by',
    ];

    protected $casts = [
        'meeting_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}