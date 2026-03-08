<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'local',
        'titular',
        'tipo',
        'tipo_cliente',
        'ci',
        'telefono',
        'direccion',
        'fechanac',
        'legalidad',
        'categoria',
        'razon',
        'nit',
        'observacion',
        'lat',
        'lng',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            // sin casts por requerimiento del proyecto
        ];
    }
}
