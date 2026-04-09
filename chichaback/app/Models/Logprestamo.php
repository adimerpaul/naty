<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logprestamo extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'cantidad',
        'motivo',
        'user_id',
        'prestamo_id',
    ];
    protected $hidden = ["created_at", "updated_at"];
    public function prestamo(){
        return $this->belongsTo(Prestamo::class)->with('cliente')->with('inventario');
    }

    public function user(){
        return $this->belongsTo(User::class);

    }
}
