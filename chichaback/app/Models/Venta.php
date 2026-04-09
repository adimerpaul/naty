<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $fillable=[
        "fecha",
        "total",
        "acuenta",
        "saldo",
        "estado",
        "tipo",
        "turno",
        "hora",
        "telefono1",
        "telefono2",
        "direccion",
        "envase",
        "observacion",
        'user_id',
        'cliente_id',
        'fechaentrega'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
    public function detalle(){
        return $this->hasOne(Detalle::class);

    }
    public function detalles(){
        return $this->hasMany(Detalle::class);

    }

    public function pagos(){
        return $this->hasMany(Pago::class);
    }
}
