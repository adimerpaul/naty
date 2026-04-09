<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable=[
        'local',
        'ci',
        'titular',
        'tipo',
        'telefono',
        'fechanac',
        'direccion',
        'legalidad',
        'categoria',
        'razon',
        'nit',
        'observacion',
        'tipo',
        'estado',
        'tipocliente'
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function prestamos(){
        return $this->hasMany(Prestamo::class)->with('inventario')->with('logprestamos');
    }
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }


}
