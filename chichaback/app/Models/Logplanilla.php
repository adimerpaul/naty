<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logplanilla extends Model
{
    use HasFactory;
    protected $fillable=[
        "tipo",
        "monto",
        "fecha",
        "hora",
        "motivo",
        'planilla_id',
    ];
}
