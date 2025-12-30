<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bidang',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relasi ke Sie
    // Tiap bidang bisa mempunyai banyak Sie
    public function sies() 
    {
        return $this->hasMany(Sie::class);
    }

    // Relasi dengan User
    // Tiap Bidang bisa mempunyak banyak User
    // Models User <One to Many> Bidang
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
