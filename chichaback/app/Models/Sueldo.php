<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sueldo extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'hora',
        'monto',
        'tipo',
        'caja',
        'observacion',
        'empleado_id',
        'user_id',

    ];

    public function empleado(){
        return $this->belongsTo(Empleado::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
