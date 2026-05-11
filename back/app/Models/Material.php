<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'unidad',
        'minimo',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'minimo' => 'decimal:2',
            'stock' => 'decimal:2',
        ];
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function recuentos()
    {
        return $this->hasMany(Recuento::class);
    }
}
