<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kwitansi',
        'tanggal',
        'nama_penerima',
        'alamat_penerima',
        'total_pembayaran',
        'total_bilangan',
        'keterangan',
        'status',
        'user_id',
        'pembelian_id',
    ];


    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
}
