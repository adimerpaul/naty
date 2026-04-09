<?php

namespace Database\Seeders;

use App\Models\Detalle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            ClienteSeeder::class,
            ProductoSeeder::class,
            VentaSeeder::class,
            DetalleSeeder::class,
            EmpleadoSeeder::class,
            SueldoSeeder::class,
            PermisoSeeder::class,
            PermisoUserSeeder::class,
            InventarioSeeder::class,
//            GastoSeeder::class
            PrestamoSeeder::class
        ]);
    }
}
