<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CicilanPembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembelian_id',
        'cicilan_ke',
        'tanggal_jatuh_tempo',
        'jumlah_cicilan',
        'status',
        'tanggal_bayar',
        'keterangan',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
}
