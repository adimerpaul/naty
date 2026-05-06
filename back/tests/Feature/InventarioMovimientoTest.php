<?php

use App\Models\Inventario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registra movimientos de inventario y permite anularlos', function () {
    $user = User::factory()->create();
    $inventario = Inventario::create([
        'nombre' => 'Bidon 20L',
        'cantidad' => 10,
        'estado' => 'ACTIVO',
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/inventarios/{$inventario->id}/movimientos", [
            'tipo' => 'DISMINUCION',
            'cantidad' => 3,
            'motivo' => 'Merma',
        ])
        ->assertCreated()
        ->assertJsonPath('tipo', 'DISMINUCION')
        ->assertJsonPath('cantidad_anterior', 10)
        ->assertJsonPath('cantidad_nueva', 7);

    expect($inventario->refresh()->cantidad)->toBe(7);

    $aumento = $this->actingAs($user, 'sanctum')
        ->postJson("/api/inventarios/{$inventario->id}/movimientos", [
            'tipo' => 'AUMENTO',
            'cantidad' => 5,
            'motivo' => 'Ingreso',
        ])
        ->assertCreated()
        ->json();

    expect($inventario->refresh()->cantidad)->toBe(12);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/inventarios/{$inventario->id}/movimientos/{$aumento['id']}/anular")
        ->assertOk()
        ->assertJsonPath('estado', 'ANULADO');

    expect($inventario->refresh()->cantidad)->toBe(7);
});
