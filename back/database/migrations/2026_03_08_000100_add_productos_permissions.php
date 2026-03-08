<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('permissions')->insertOrIgnore([
            ['name' => 'Producto Detalle', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Producto Local', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', ['Producto Detalle', 'Producto Local'])
            ->delete();
    }
};

