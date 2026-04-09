<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'unid',
        'min',
        'stock'
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function Recuentos(){
        return $this->hasMany(Recuento::class);
    }

    public function Compras(){
        return $this->hasMany(Compra::class);
    }
}
