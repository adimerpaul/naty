<?php

use App\Models\Caja;
use App\Models\Pago;
use App\Models\Personal;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function crearIngresoCaja(int $cajaId, float $monto, ?int $userId = null): void
{
    $venta = Venta::create([
        'caja_id' => $cajaId,
        'cliente_id' => null,
        'user_id' => $userId,
        'tipo_venta' => 'caja',
        'tipo_movimiento' => 'ingreso',
        'tipo_pago' => 'contado',
        'estado' => 'ACTIVA',
        'fecha_venta' => now()->toDateString(),
        'cliente_nombre' => null,
        'cliente_telefono' => null,
        'cliente_direccion' => null,
        'total' => $monto,
        'observacion' => 'Ingreso inicial',
    ]);

    Pago::create([
        'venta_id' => $venta->id,
        'user_id' => $userId,
        'nro_cuota' => 1,
        'monto' => $monto,
        'fecha_programada' => now()->toDateString(),
        'fecha_pago' => now(),
        'metodo' => 'efectivo',
        'estado' => 'PAGADO',
        'observacion' => 'Ingreso inicial',
    ]);
}

it('rechaza un egreso manual mayor al saldo de la caja', function () {
    $user = User::factory()->create();
    $caja = Caja::create([
        'nombre' => 'Caja General',
        'descripcion' => 'Caja principal',
        'estado' => true,
    ]);

    crearIngresoCaja($caja->id, 100, $user->id);

    $response = $this->actingAs($user)->postJson('/api/cajas/movimientos', [
        'modo' => 'manual',
        'tipo_movimiento' => 'egreso',
        'caja_id' => $caja->id,
        'monto' => 150,
        'observacion' => 'Gasto mayor al saldo',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['monto']);

    expect($response->json('errors.monto.0'))->toContain('Fondos insuficientes en caja');
});

it('rechaza una transferencia mayor al saldo de la caja origen', function () {
    $user = User::factory()->create();
    $cajaOrigen = Caja::create([
        'nombre' => 'Caja General',
        'descripcion' => 'Caja principal',
        'estado' => true,
    ]);
    $cajaDestino = Caja::create([
        'nombre' => 'Caja Chica',
        'descripcion' => 'Caja secundaria',
        'estado' => true,
    ]);

    crearIngresoCaja($cajaOrigen->id, 80, $user->id);

    $response = $this->actingAs($user)->postJson('/api/cajas/movimientos', [
        'modo' => 'transferencia',
        'origen_caja_id' => $cajaOrigen->id,
        'destino_caja_id' => $cajaDestino->id,
        'monto' => 120,
        'observacion' => 'Transferencia excesiva',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['monto']);

    expect($response->json('errors.monto.0'))->toContain('Fondos insuficientes en caja');
});

it('rechaza un egreso de venta mayor al saldo disponible en caja', function () {
    $user = User::factory()->create();
    $caja = Caja::create([
        'nombre' => 'Caja General',
        'descripcion' => 'Caja principal',
        'estado' => true,
    ]);

    crearIngresoCaja($caja->id, 90, $user->id);

    $response = $this->actingAs($user)->postJson('/api/ventas', [
        'caja_id' => $caja->id,
        'tipo_venta' => 'detalle',
        'tipo_movimiento' => 'egreso',
        'tipo_pago' => 'contado',
        'metodo_pago' => 'efectivo',
        'monto' => 140,
        'concepto' => 'Gasto operativo',
        'observacion' => 'No debe dejar saldo negativo',
        'items' => [],
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['monto']);

    expect($response->json('errors.monto.0'))->toContain('Fondos insuficientes en caja');
});

it('rechaza un adelanto de personal mayor al saldo disponible en caja', function () {
    $user = User::factory()->create();
    $caja = Caja::create([
        'nombre' => 'Caja General',
        'descripcion' => 'Caja principal',
        'estado' => true,
    ]);
    $personal = Personal::create([
        'ci' => '123456',
        'nombre' => 'Juan Perez',
        'salario' => 500,
        'estado' => 'ACTIVO',
    ]);

    crearIngresoCaja($caja->id, 60, $user->id);

    $response = $this->actingAs($user)->postJson('/api/personal-pagos', [
        'personal_id' => $personal->id,
        'caja_id' => $caja->id,
        'mes' => now()->format('Y-m'),
        'tipo_registro' => 'adelanto',
        'monto' => 100,
        'fecha_pago' => now()->toDateString(),
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['monto']);

    expect($response->json('errors.monto.0'))->toContain('Fondos insuficientes en caja');
});
