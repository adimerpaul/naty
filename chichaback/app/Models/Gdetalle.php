<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gdetalle extends Model
{
    use HasFactory;
    protected $fillable=[
        'garantia_id',
        'user_id',
        'inventario_id',
        'cantidad',
        'nombreinv',
    ];
    protected $hidden = ["created_at", "updated_at"];
}

