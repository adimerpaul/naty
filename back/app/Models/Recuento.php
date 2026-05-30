<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recuento extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'cantidad',
        'observacion',
        'material_id',
        'compra_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'cantidad' => 'decimal:2',
        ];
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
