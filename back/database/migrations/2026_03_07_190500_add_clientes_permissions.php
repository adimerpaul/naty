<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('permissions')->insertOrIgnore([
            ['name' => 'Cliente Detalle', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cliente Local', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', ['Cliente Detalle', 'Cliente Local'])
            ->delete();
    }
};

