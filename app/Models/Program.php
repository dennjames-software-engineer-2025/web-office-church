<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_program',
        'deskripsi',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}