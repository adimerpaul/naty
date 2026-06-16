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

it('pagina prestamos y filtra por inventario', function () {
    $user = User::factory()->create();
    $cliente = Cliente::create(['nombre' => 'Local', 'tipo_cliente' => 'local', 'estado' => true]);
    $inventarioBidon = Inventario::create(['nombre' => 'Bidon', 'cantidad' => 10, 'estado' => 'ACTIVO']);
    $inventarioCaja = Inventario::create(['nombre' => 'Caja', 'cantidad' => 10, 'estado' => 'ACTIVO']);

    foreach (range(1, 3) as $i) {
        Prestamo::create([
            'fecha' => now()->toDateString(),
            'tipo' => 'prestamo',
            'estado' => 'EN PRESTAMO',
            'cantidad' => 1,
            'cliente_id' => $cliente->id,
            'inventario_id' => $inventarioBidon->id,
        ]);
    }

    Prestamo::create([
        'fecha' => now()->toDateString(),
        'tipo' => 'prestamo',
        'estado' => 'EN PRESTAMO',
        'cantidad' => 1,
        'cliente_id' => $cliente->id,
        'inventario_id' => $inventarioCaja->id,
    ]);

    $response = $this->actingAs($user)->getJson(
        "/api/prestamos?tipo_venta=local&inventario_id={$inventarioBidon->id}&per_page=2&page=1"
    );

    $response->assertOk();
    expect($response->json('total'))->toBe(3);
    expect($response->json('per_page'))->toBe(2);
    expect($response->json('data'))->toHaveCount(2);
    expect(collect($response->json('data'))->pluck('inventario_id')->unique()->values()->all())
        ->toBe([$inventarioBidon->id]);
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
