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
        'tipo_cliente',
        'ci',
        'telefono',
        'direccion',
        'observacion',
        'lat',
        'lng',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
        ];
    }
}
