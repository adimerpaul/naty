<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'caja_id',
        'cliente_id',
        'user_id',
        'tipo_venta',
        'tipo_movimiento',
        'tipo_pago',
        'estado',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        'total',
        'observacion',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
