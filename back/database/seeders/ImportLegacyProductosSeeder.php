<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportLegacyProductosSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = DB::connection('chicheria')->table('productos')->orderBy('id');
        $total = $legacy->count();

        if ($total === 0) {
            $this->command?->warn('No se encontraron productos en la base legacy (chicheria).');
            return;
        }

        $this->command?->info("Iniciando migracion de {$total} productos...");

        $migrados = 0;
        $legacy->chunkById(500, function ($rows) use (&$migrados) {
            $payload = [];

            foreach ($rows as $row) {
                $payload[] = [
                    'id' => (int) $row->id,
                    'nombre' => trim((string) ($row->nombre ?: ('Producto ' . $row->id))),
                    'precio' => (float) ($row->precio ?? 0),
                    'observacion' => $this->nullableTrim($row->observacion ?? null),
                    'grupo' => $this->mapGrupo($row->grupo ?? null),
                    'tipo_producto' => $this->mapTipo($row->tipo ?? null),
                    'orden' => max(1, (int) ($row->orden ?? 1)),
                    'color' => $this->mapColor($row->color ?? null),
                    'fotografia' => null,
                    'estado' => strtoupper((string) ($row->estado ?? 'ACTIVO')) === 'ACTIVO',
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                    'deleted_at' => null,
                ];
            }

            DB::table('productos')->upsert(
                $payload,
                ['id'],
                [
                    'nombre',
                    'precio',
                    'observacion',
                    'grupo',
                    'tipo_producto',
                    'orden',
                    'color',
                    'estado',
                    'updated_at',
                ]
            );

            $migrados += count($payload);
            $this->command?->info("Migrados {$migrados} productos...");
        });

        $this->command?->info("Migracion de productos finalizada. Total: {$migrados}.");
    }

    private function mapTipo(?string $tipo): string
    {
        $tipo = strtolower(trim((string) $tipo));
        return str_contains($tipo, 'detalle') ? 'detalle' : 'local';
    }

    private function mapGrupo(?string $grupo): string
    {
        $g = strtolower(trim((string) $grupo));
        if (str_contains($g, 'garapi')) return 'garapina';
        return 'chicha';
    }

    private function mapColor(?string $color): string
    {
        $c = trim((string) $color);
        return preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? $c : '#ffffff';
    }

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) return null;
        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }
}

