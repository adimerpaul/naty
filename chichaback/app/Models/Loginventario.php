<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loginventario extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'cantidad',
        'agregar',
        'Motivo',
        'producto_id',
        'user_id',
    ];
    protected $hidden = ["created_at", "updated_at"];
}
