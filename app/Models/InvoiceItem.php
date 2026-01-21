<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'nama_barang',
        'qty',
        'harga_satuan',
        'total_harga',
        'estimasi_ongkir',  
        'penandatangan',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
