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
        'bidang_id',
        'created_by',
        'target_kedudukan',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
