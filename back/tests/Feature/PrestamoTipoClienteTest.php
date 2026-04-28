<?php

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filtra prestamos por tipo de cliente', function () {
    $user = User::factory()->create();
    $clienteDetalle = Cliente::create(['nombre' => 'Detalle', 'tipo_cliente' => 'detalle', 'estado' => true]);
    $clienteLocal = Cliente::create(['nombre' => 'Local', 'tipo_cliente' => 'local', 'estado' => true]);
    $inventario = Inventario::create(['nombre' => 'Bidon', 'cantidad' => 10, 'estado' => 'ACTIVO']);

    Prestamo::create([
        'fecha' => now()->toDateString(),
        'tipo' => 'prestamo',
        'estado' => 'EN PRESTAMO',
        'cantidad' => 1,
        'cliente_id' => $clienteDetalle->id,
        'inventario_id' => $inventario->id,
    ]);
    Prestamo::create([
        'fecha' => now()->toDateString(),
        'tipo' => 'prestamo',
        'estado' => 'EN PRESTAMO',
        'cantidad' => 1,
        'cliente_id' => $clienteLocal->id,
        'inventario_id' => $inventario->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/prestamos?tipo_venta=detalle');

    $response->assertOk();
    expect($response->json())->toHaveCount(1);
    expect($response->json('0.cliente.tipo_cliente'))->toBe('detalle');
});

it('rechaza prestamos cuando el cliente no corresponde al tipo seleccionado', function () {
    $user = User::factory()->create();
    $clienteLocal = Cliente::create(['nombre' => 'Local', 'tipo_cliente' => 'local', 'estado' => true]);
    $inventario = Inventario::create(['nombre' => 'Bidon', 'cantidad' => 10, 'estado' => 'ACTIVO']);

    $response = $this->actingAs($user)->postJson('/api/prestamos', [
        'tipo' => 'prestamo',
        'tipo_venta' => 'detalle',
        'cliente_id' => $clienteLocal->id,
        'inventario_id' => $inventario->id,
        'cantidad' => 1,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cliente_id']);
});
