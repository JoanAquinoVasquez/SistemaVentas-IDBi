<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'nombre', 'precio_unitario', 'stock'];

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->sku = strtoupper($product->sku);
        });

        static::updating(function ($product) {
            $product->sku = strtoupper($product->sku);
        });
    }
}
