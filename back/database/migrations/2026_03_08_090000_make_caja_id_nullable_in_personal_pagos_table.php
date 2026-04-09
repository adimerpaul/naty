<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('personal_pagos', function (Blueprint $table) {
                $table->unsignedBigInteger('caja_id')->nullable()->change();
            });

            return;
        }

        DB::statement('ALTER TABLE personal_pagos MODIFY caja_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('personal_pagos', function (Blueprint $table) {
                $table->unsignedBigInteger('caja_id')->nullable(false)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE personal_pagos MODIFY caja_id BIGINT UNSIGNED NOT NULL');
    }
};
