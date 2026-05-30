<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'cantidad',
        'retiro',
        'costo',
        'subtotal',
        'deuda',
        'fechaven',
        'lote',
        'comentario',
        'observacion',
        'estado',
        'material_id',
        'user_id',
        'provider_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'fechaven' => 'date:Y-m-d',
            'cantidad' => 'decimal:2',
            'retiro' => 'decimal:2',
            'costo' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'deuda' => 'decimal:2',
        ];
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pagos()
    {
        return $this->hasMany(LogCompra::class);
    }

    public function recuentos()
    {
        return $this->hasMany(Recuento::class);
    }
}
