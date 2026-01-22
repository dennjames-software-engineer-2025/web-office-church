<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kedudukan',
        'nama_bidang',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForKedudukan($query, string $kedudukan)
    {
        return $query->where('kedudukan', $kedudukan);
    }

    public function sies()
    {
        return $this->hasMany(Sie::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function templates()
    {
        return $this->belongsToMany(Template::class, 'template_bidang')
            ->withTimestamps();
    }

    public function ketua()
    {
        return $this->hasOne(User::class)->where('jabatan', 'ketua_bidang');
    }
}
