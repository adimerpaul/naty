<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_pagos', function (Blueprint $table) {
            $table->string('tipo_registro', 20)->default('salario')->after('mes');
        });
    }

    public function down(): void
    {
        Schema::table('personal_pagos', function (Blueprint $table) {
            $table->dropColumn('tipo_registro');
        });
    }
};

