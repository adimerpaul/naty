<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'tipo',
        'estado',
        'efectivo',
        'fisico',
        'observacion',
        'cantidad',
        'prestado',
        'fechaAnulacion',
        'motivoAnulacion',
        'user_id',
        'cliente_id',
        'inventario_id',
        'venta_id',
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
