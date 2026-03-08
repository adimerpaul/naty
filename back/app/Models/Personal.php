<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table='personales';

    protected $fillable = [
        'ci',
        'nombre',
        'salario',
        'fechanac',
        'dias',
        'celular',
        'tipo',
        'fechaingreso',
        'estado',
        'fotografia',
    ];
}

