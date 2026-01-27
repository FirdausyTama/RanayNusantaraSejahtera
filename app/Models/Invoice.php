<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
     'tanggal_invoice',
    'nomor_invoice',
    'nama_penerima',
    'pembelian_id',
    'total_pembayaran',
    'berat_total',
    'harga_per_kg',
    'estimasi_ongkir',
    'penandatangan',
    'user_id',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
    ];


    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
