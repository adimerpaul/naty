<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    use HasFactory;
    protected $fillable=[
        'fechainicio',
        'fechafin',
        'fechapago',
        'monto',
        'adelanto',
        'descuento',
        'bono',
        'restante',
        'total',
        'observacion',
        'estado',
        'empleado_id',
    ];
}
