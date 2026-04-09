<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;
    protected $fillable=[
        'venta_id',
        'user_id',
        'producto_id',
        'cantidad',
        'nombreproducto',
        'precio',
        'subtotal',
    ];
}
