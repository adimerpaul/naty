<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'monto',
        'observacion',
        'venta_id',
        'user_id',
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function venta(){
        return $this->belongsTo(Venta::class)->with('cliente')->with('detalles');
    }
}
