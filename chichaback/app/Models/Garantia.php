<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'efectivo',
        'fisico',
        'observacion',
        'estado',
        'cantidad',
        'user_id',
        'cliente_id',
        'inventario_id',
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
    public function inventario(){
        return $this->belongsTo(Inventario::class);
    }
}
