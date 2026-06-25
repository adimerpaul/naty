<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('permissions')->insertOrIgnore([
            ['name' => 'HistorialVentas', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ResumenGastosVentas', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', ['HistorialVentas', 'ResumenGastosVentas'])
            ->delete();
    }
};
