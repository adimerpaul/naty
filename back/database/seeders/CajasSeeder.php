<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CajasSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('cajas')->upsert([
            [
                'id' => 1,
                'nombre' => 'Caja General',
                'descripcion' => 'Caja principal para compras y ventas',
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'Caja Chica',
                'descripcion' => 'Caja secundaria para operaciones menores',
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['nombre', 'descripcion', 'estado', 'updated_at']);
    }
}

