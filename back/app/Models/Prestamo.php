<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestamo extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = [
        'retornado_cantidad',
        'cantidad_actual',
        'retornado_efectivo',
        'efectivo_actual',
        'fisico_recibido',
        'monto_pendiente',
    ];

    protected $fillable = [
        'fecha',
        'tipo',
        'estado',
        'efectivo',
        'fisico',
        'observacion',
        'cantidad',
        'prestado',
        'fechaAnulacion',
        'motivoAnulacion',
        'user_id',
        'cliente_id',
        'inventario_id',
        'venta_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'fechaAnulacion' => 'date:Y-m-d',
            'prestado' => 'boolean',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function retornos()
    {
        return $this->hasMany(PrestamoRetorno::class);
    }

    public function getRetornadoCantidadAttribute(): int
    {
        return (int) $this->sumaRetornos('cantidad');
    }

    public function getCantidadActualAttribute(): int
    {
        return max(0, (int) $this->cantidad - $this->retornado_cantidad);
    }

    public function getRetornadoEfectivoAttribute(): float
    {
        return round((float) $this->sumaRetornos('efectivo'), 2);
    }

    public function getEfectivoActualAttribute(): float
    {
        return $this->monto_pendiente;
    }

    public function getFisicoRecibidoAttribute(): float
    {
        $monto = round((float) $this->efectivo, 2);
        if ($monto <= 0 && is_numeric($this->fisico)) {
            $monto = round((float) $this->fisico, 2);
        }

        return $monto;
    }

    public function getMontoPendienteAttribute(): float
    {
        return max(0, round($this->fisico_recibido - $this->retornado_efectivo, 2));
    }

    private function sumaRetornos(string $campo): float
    {
        if ($this->relationLoaded('retornos')) {
            return (float) $this->retornos->sum($campo);
        }

        return (float) $this->retornos()->sum($campo);
    }
}
