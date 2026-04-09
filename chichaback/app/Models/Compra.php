<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha',
        'hora',
        'costo',
        'cantidad',
        'retiro',
        'subtotal',
        'deuda',
        'comentario',
        'observacion',
        'lote',
        'estado',
        'provider_id',
        'material_id',
        'user_id'

    ];
    protected $hidden = ["created_at", "updated_at"];

    public function material(){
        return $this->belongsTo(Material::class);
    }

    public function recuentos(){
        return $this->hasMany(Recuento::class);
    }

    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function logcompras(){
        return $this->hasMany(Logcompra::class);
    }
}
