<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrestamoRetorno extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prestamo_id',
        'user_id',
        'fecha',
        'cantidad',
        'efectivo',
        'fisico',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'cantidad' => 'integer',
            'efectivo' => 'decimal:2',
        ];
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
