<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_id',
        'caja_id',
        'user_id',
        'venta_id',
        'venta_reversion_id',
        'mes',
        'tipo_registro',
        'sueldo',
        'extras',
        'adelantos',
        'descuentos',
        'monto_pagado',
        'estado',
        'observacion',
        'fecha_pago',
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class);
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
