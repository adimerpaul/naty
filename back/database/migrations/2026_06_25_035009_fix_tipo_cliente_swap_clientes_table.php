<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // La migración original invirtió los valores: tipocliente=1 (LOCAL) quedó como 'detalle'
    // y tipocliente=2 (DETALLE) quedó como 'local'. Se intercambian usando un valor temporal.
    public function up(): void
    {
        DB::table('clientes')->where('tipo_cliente', 'local')->update(['tipo_cliente' => '_tmp']);
        DB::table('clientes')->where('tipo_cliente', 'detalle')->update(['tipo_cliente' => 'local']);
        DB::table('clientes')->where('tipo_cliente', '_tmp')->update(['tipo_cliente' => 'detalle']);
    }

    public function down(): void
    {
        DB::table('clientes')->where('tipo_cliente', 'local')->update(['tipo_cliente' => '_tmp']);
        DB::table('clientes')->where('tipo_cliente', 'detalle')->update(['tipo_cliente' => 'local']);
        DB::table('clientes')->where('tipo_cliente', '_tmp')->update(['tipo_cliente' => 'detalle']);
    }
};
