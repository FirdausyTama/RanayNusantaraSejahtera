<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokImage extends Model
{
    protected $fillable = ['stok_id', 'image_path'];

    public function stok()
    {
        return $this->belongsTo(Stok::class);
    }
}
