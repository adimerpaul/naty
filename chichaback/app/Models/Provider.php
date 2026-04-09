<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $fillable=[
        'razon',
        'nombre',
        'nit',
        'direccion',
        'email',
        'telefono',
        'estado'

    ];
    protected $hidden = ["created_at", "updated_at"];


}
