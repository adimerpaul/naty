<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable=[
        'precio',
        'observacion',
        'glosa',
        'fecha',
        'hora',
        'glosa_id',
        'user_id',
        'sueldopgas_id',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function glosa(){
        return $this->belongsTo(Glosa::class);
    }
}
