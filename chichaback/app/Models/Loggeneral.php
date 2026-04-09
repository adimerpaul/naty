<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loggeneral extends Model
{
    use HasFactory;
    protected $fillable=[
        'numero',
        'monto',
        'detalle',
        'motivo',
        'tipo',
        'fecha',
        'hora',
        'glosa_id',
        'user_id',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function glosa(){
        return $this->belongsTo(Glosa::class);
    }
}
