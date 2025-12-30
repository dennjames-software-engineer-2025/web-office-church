<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sie',
        'bidang_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relasi ke Bidang
    // Setiap Sie hanyak punya 1 bidang
    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    // Relasi ke User
    // Setiap Sie bisa memiliki banyak User
    // Models User <One to Many> Sie
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
