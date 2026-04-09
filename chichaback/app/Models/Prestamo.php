<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'fechaAnulacion',
        'efectivo',
        'estado',
        'estado',
        'observacion',
        'cantidad',
        'user_id',
        'cliente_id',
        'inventario_id',
    ];
    protected $hidden = [ "updated_at"];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function inventario(){
        return $this->belongsTo(Inventario::class);
    }

    public function logprestamos(){
        return $this->hasMany(Logprestamo::class);
    }

}
