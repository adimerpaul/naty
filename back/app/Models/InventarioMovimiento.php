<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventarioMovimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'inventario_id',
        'user_id',
        'fecha',
        'tipo',
        'estado',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva',
        'motivo',
        'anulado_at',
        'anulado_por',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'anulado_at' => 'datetime',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anuladoPor()
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }
}
