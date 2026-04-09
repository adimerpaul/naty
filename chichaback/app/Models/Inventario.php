<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $fillable=[
        'codigo',
        'nombre',
        'cantidad',
        'precio',
        'detalle',
        'orden',
        'estado',
    ];
    protected $hidden = ["created_at", "updated_at"];
    public function prestamos(){
        return $this->hasMany(Prestamo::class);
    }
}
