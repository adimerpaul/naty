<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'caja_id',
        'cliente_id',
        'user_id',
        'tipo_venta',
        'tipo_movimiento',
        'tipo_pago',
        'estado',
        'fecha_venta',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        'total',
        'observacion',
        'deuda_oculta',
        'facturado',
        'factura_estado',
        'factura_error',
        'cuf',
        'cufd',
        'leyenda',
        'online',
        'siat_codigo_recepcion',
        'factura_xml_path',
        'factura_gz_path',
    ];

    protected $casts = [
        'fecha_venta' => 'date:Y-m-d',
        'deuda_oculta' => 'boolean',
        'facturado' => 'boolean',
        'online' => 'boolean',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
}
