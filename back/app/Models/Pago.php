<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'user_id',
        'nro_cuota',
        'monto',
        'fecha_programada',
        'fecha_pago',
        'metodo',
        'estado',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }
}
