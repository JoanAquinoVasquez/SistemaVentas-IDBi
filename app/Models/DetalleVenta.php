<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = ['venta_id', 'product_id', 'cantidad'];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
