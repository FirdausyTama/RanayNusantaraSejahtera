<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $fillable = [
        'stok_id',
        'user_id',
        'jenis',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'keterangan',
    ];

    public function stok()
    {
        return $this->belongsTo(Stok::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
