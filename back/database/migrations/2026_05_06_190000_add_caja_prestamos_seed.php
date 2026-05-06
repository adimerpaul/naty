<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cajas')->updateOrInsert(
            ['id' => 3],
            [
                'nombre' => 'Caja Prestamos',
                'descripcion' => 'Caja exclusiva para operaciones de prestamos y bajas',
                'estado' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('cajas')->where('id', 3)->delete();
    }
};

