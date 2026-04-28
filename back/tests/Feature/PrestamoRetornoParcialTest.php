<?php

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registra retorno parcial y calcula saldos del prestamo', function () {
    $user = User::factory()->create();
    $cliente = Cliente::create(['nombre' => 'Cliente detalle', 'tipo_cliente' => 'detalle', 'estado' => true]);
    $inventario = Inventario::create(['nombre' => 'Bidon', 'cantidad' => 2, 'estado' => 'ACTIVO', 'precio' => 15]);
    $prestamo = Prestamo::create([
        'fecha' => now()->toDateString(),
        'tipo' => 'prestamo',
        'estado' => 'EN PRESTAMO',
        'cantidad' => 10,
        'efectivo' => 100,
        'fisico' => 'garantia',
        'cliente_id' => $cliente->id,
        'inventario_id' => $inventario->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/prestamos/{$prestamo->id}/retorno-parcial", [
        'cantidad' => 3,
        'efectivo' => 25,
        'fisico' => 'parte fisica',
        'observacion' => 'Primer retorno',
    ]);

    $response->assertOk();

    expect($inventario->refresh()->cantidad)->toBe(5);
    expect($prestamo->refresh()->estado)->toBe('PARCIAL');
    expect($response->json('retornado_cantidad'))->toBe(3);
    expect($response->json('cantidad_actual'))->toBe(7);
    expect((float) $response->json('fisico_recibido'))->toBe(100.0);
    expect((float) $response->json('retornado_efectivo'))->toBe(25.0);
    expect((float) $response->json('efectivo_actual'))->toBe(75.0);
    expect((float) $response->json('monto_pendiente'))->toBe(75.0);
    expect($response->json('retornos'))->toHaveCount(1);
});
