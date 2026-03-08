<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'user_id',
        'producto_nombre',
        'precio',
        'cantidad',
        'subtotal',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'cantidad' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }
}

