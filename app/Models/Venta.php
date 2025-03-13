<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'nombre_cl', 'num_iden_cl', 'correo_cl', 'user_id', 'monto_total', 'fecha_hora'];

    public $timestamps = false; // La migración no tiene timestamps

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}
