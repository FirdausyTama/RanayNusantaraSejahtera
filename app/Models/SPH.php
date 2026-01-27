<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPH extends Model
{
    use HasFactory;

    protected $table = 'surat_penawarans';
    protected $fillable = [
        'nomor_sph',
        'tanggal',
        'tempat',
        'lampiran',
        'hal',
        'jabatan_tujuan',
        'nama_perusahaan',
        'alamat',
        'detail_barang',
        'total_keseluruhan',
        'penandatangan',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'detail_barang' => 'array',
        'tanggal' => 'date',
    ];

    public function lampiranGambar()
    {
        return $this->hasMany(SphLampiranGambar::class, 'sph_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
