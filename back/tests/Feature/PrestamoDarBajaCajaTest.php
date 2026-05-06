<?php

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('da de baja un prestamo y registra ingreso en caja prestamos', function () {
    $user = User::factory()->create();
    Caja::updateOrCreate(
        ['id' => 3],
        ['nombre' => 'Caja Prestamos', 'descripcion' => 'Caja de prestamos', 'estado' => true]
    );

    $cliente = Cliente::create([
        'nombre' => 'Cliente D',
        'titular' => 'Cliente D',
        'tipo_cliente' => 'detalle',
        'estado' => true,
    ]);
    $inventario = Inventario::create([
        'nombre' => 'Bidon',
        'cantidad' => 5,
        'estado' => 'ACTIVO',
        'precio' => 20,
    ]);

    $prestamo = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prestamos', [
            'tipo' => 'prestamo',
            'cliente_id' => $cliente->id,
            'inventario_id' => $inventario->id,
            'cantidad' => 2,
            'efectivo' => 40,
            'tipo_venta' => 'detalle',
        ])
        ->assertCreated()
        ->json();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/prestamos/{$prestamo['id']}/dar-baja", [
            'monto' => 30,
            'observacion' => 'Baja test',
        ])
        ->assertOk()
        ->assertJsonPath('estado', 'BAJA');

    $resumen = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prestamos/caja/resumen?tipo_venta=detalle')
        ->assertOk()
        ->json();

    expect((float) $resumen['caja_total'])->toBe(30.0);
});

it('registra venta de material de prestamos directamente en caja prestamos', function () {
    $user = User::factory()->create();
    Caja::updateOrCreate(
        ['id' => 3],
        ['nombre' => 'Caja Prestamos', 'descripcion' => 'Caja de prestamos', 'estado' => true]
    );

    $cliente = Cliente::create([
        'nombre' => 'Cliente Venta Material',
        'titular' => 'Cliente Venta Material',
        'tipo_cliente' => 'detalle',
        'estado' => true,
    ]);
    $inventario = Inventario::create([
        'nombre' => 'Bidon',
        'cantidad' => 5,
        'estado' => 'ACTIVO',
        'precio' => 20,
    ]);

    $prestamo = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prestamos', [
            'tipo' => 'venta',
            'cliente_id' => $cliente->id,
            'inventario_id' => $inventario->id,
            'cantidad' => 2,
            'efectivo' => 40,
            'tipo_venta' => 'detalle',
            'metodo_pago' => 'efectivo',
        ])
        ->assertCreated()
        ->assertJsonPath('tipo', 'venta')
        ->assertJsonPath('estado', 'VENDIDO')
        ->json();

    $venta = Venta::with('pagos')->findOrFail($prestamo['venta_id']);

    expect($venta->caja_id)->toBe(3)
        ->and($venta->tipo_venta)->toBe('caja_prestamos')
        ->and((float) $venta->pagos->sum('monto'))->toBe(40.0);

    $resumen = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prestamos/caja/resumen?tipo_venta=detalle')
        ->assertOk()
        ->json();

    expect((float) $resumen['vendidos_monto'])->toBe(40.0)
        ->and((float) $resumen['caja_total'])->toBe(40.0);
});

it('genera reporte pdf de prestamos y venta de material', function () {
    $user = User::factory()->create();
    Caja::updateOrCreate(
        ['id' => 3],
        ['nombre' => 'Caja Prestamos', 'descripcion' => 'Caja de prestamos', 'estado' => true]
    );

    $cliente = Cliente::create([
        'nombre' => 'Cliente Reporte',
        'titular' => 'Cliente Reporte',
        'tipo_cliente' => 'detalle',
        'estado' => true,
    ]);
    $inventario = Inventario::create([
        'nombre' => 'Canastillo',
        'cantidad' => 10,
        'estado' => 'ACTIVO',
        'precio' => 15,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/prestamos', [
            'tipo' => 'prestamo',
            'cliente_id' => $cliente->id,
            'inventario_id' => $inventario->id,
            'cantidad' => 2,
            'efectivo' => 30,
            'tipo_venta' => 'detalle',
        ])
        ->assertCreated();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/prestamos/reporte/pdf?tipo_venta=detalle')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
