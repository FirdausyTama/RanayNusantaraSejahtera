<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SphLampiranGambar extends Model
{
    use HasFactory;

    protected $table = 'sph_lampiran_gambar';

    protected $fillable = [
        'sph_id',
        'path_gambar',
        'nama_file',
    ];

    public function sph()
    {
        return $this->belongsTo(SPH::class, 'sph_id');
    }
}
