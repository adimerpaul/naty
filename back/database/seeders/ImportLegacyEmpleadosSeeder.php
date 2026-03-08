<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportLegacyEmpleadosSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = DB::connection('chicheria')->table('empleados')->orderBy('id');
        $total = $legacy->count();
        if ($total === 0) {
            $this->command?->warn('No se encontraron empleados en chicheria.');
            return;
        }

        $migrados = 0;
        $legacy->chunkById(500, function ($rows) use (&$migrados) {
            $payload = [];
            foreach ($rows as $row) {
                $payload[] = [
                    'id' => (int) $row->id,
                    'ci' => $this->safeText($row->ci, 'SIN-CI-' . $row->id),
                    'nombre' => $this->safeText($row->nombre, 'SIN REGISTRO'),
                    'salario' => (float) ($row->salario ?? 0),
                    'fechanac' => $row->fechanac,
                    'dias' => max(0, (int) ($row->dias ?? 0)),
                    'celular' => $this->safeNullable($row->celular),
                    'tipo' => $this->safeNullable($row->tipo),
                    'fechaingreso' => $row->fechaingreso,
                    'estado' => strtoupper((string) ($row->estado ?? 'ACTIVO')) === 'INACTIVO' ? 'INACTIVO' : 'ACTIVO',
                    'fotografia' => null,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                    'deleted_at' => null,
                ];
            }

            DB::table('personales')->upsert(
                $payload,
                ['id'],
                ['ci', 'nombre', 'salario', 'fechanac', 'dias', 'celular', 'tipo', 'fechaingreso', 'estado', 'updated_at']
            );
            $migrados += count($payload);
            $this->command?->info("Personal migrado: {$migrados}");
        });
    }

    private function safeText(mixed $value, string $fallback): string
    {
        $txt = trim((string) $value);
        return $txt === '' ? $fallback : $txt;
    }

    private function safeNullable(mixed $value): ?string
    {
        $txt = trim((string) $value);
        return $txt === '' ? null : $txt;
    }
}

