<?php

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retorna inventario de prestamos asociados al anular una venta', function () {
    $user = User::factory()->create();
    $caja = Caja::create([
        'nombre' => 'Caja General',
        'descripcion' => 'Caja principal',
        'estado' => true,
    ]);
    $cliente = Cliente::create([
        'nombre' => 'Cliente detalle',
        'tipo_cliente' => 'detalle',
        'estado' => true,
    ]);
    $inventario = Inventario::create([
        'nombre' => 'Bidon verde',
        'cantidad' => 2,
        'estado' => 'ACTIVO',
        'precio' => 50,
    ]);
    $venta = Venta::create([
        'caja_id' => $caja->id,
        'cliente_id' => $cliente->id,
        'user_id' => $user->id,
        'tipo_venta' => 'detalle',
        'tipo_movimiento' => 'ingreso',
        'tipo_pago' => 'contado',
        'estado' => 'ACTIVA',
        'fecha_venta' => now()->toDateString(),
        'cliente_nombre' => $cliente->nombre,
        'total' => 100,
    ]);

    VentaDetalle::create([
        'venta_id' => $venta->id,
        'user_id' => $user->id,
        'producto_nombre' => 'Chicha',
        'precio' => 100,
        'cantidad' => 1,
        'subtotal' => 100,
        'estado' => true,
    ]);
    Pago::create([
        'venta_id' => $venta->id,
        'user_id' => $user->id,
        'nro_cuota' => 1,
        'monto' => 100,
        'fecha_programada' => now()->toDateString(),
        'fecha_pago' => now(),
        'metodo' => 'efectivo',
        'estado' => 'PAGADO',
    ]);
    $prestamo = Prestamo::create([
        'fecha' => now()->toDateString(),
        'tipo' => 'prestamo',
        'estado' => 'EN PRESTAMO',
        'cantidad' => 3,
        'prestado' => false,
        'user_id' => $user->id,
        'cliente_id' => $cliente->id,
        'inventario_id' => $inventario->id,
        'venta_id' => $venta->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/ventas/{$venta->id}/anular");

    $response->assertOk();

    expect($venta->refresh()->estado)->toBe('ANULADA');
    expect($inventario->refresh()->cantidad)->toBe(5);
    expect($prestamo->refresh()->estado)->toBe('ANULADO');
    expect($prestamo->prestado)->toBeFalse();
});
