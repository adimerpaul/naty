<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportLegacyInventariosSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = DB::connection('chicheria')->table('inventarios')->orderBy('id');
        $total = $legacy->count();
        if ($total === 0) {
            $this->command?->warn('No se encontraron inventarios en chicheria.');
            return;
        }

        $migrados = 0;
        $legacy->chunkById(500, function ($rows) use (&$migrados) {
            $payload = [];
            foreach ($rows as $row) {
                $payload[] = [
                    'id' => (int) $row->id,
                    'codigo' => $this->nullableTrim($row->codigo),
                    'fecha' => $row->fecha ?? now()->toDateString(),
                    'nombre' => $this->nullableTrim($row->nombre) ?: ('Inventario ' . $row->id),
                    'cantidad' => max(0, (int) ($row->cantidad ?? 0)),
                    'detalle' => $this->nullableTrim($row->detalle),
                    'orden' => (int) ($row->orden ?? 0),
                    'estado' => strtoupper((string) ($row->estado ?? 'ACTIVO')),
                    'precio' => (float) ($row->precio ?? 0),
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                    'deleted_at' => null,
                ];
            }
            DB::table('inventarios')->upsert(
                $payload,
                ['id'],
                ['codigo', 'fecha', 'nombre', 'cantidad', 'detalle', 'orden', 'estado', 'precio', 'updated_at']
            );
            $migrados += count($payload);
            $this->command?->info("Inventarios migrados: {$migrados}");
        });
    }

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) return null;
        $txt = trim((string) $value);
        return $txt === '' ? null : $txt;
    }
}

