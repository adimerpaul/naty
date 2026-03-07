<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportLegacyClientesSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = DB::connection('chicheria')->table('clientes')->orderBy('id');
        $total = $legacy->count();

        if ($total === 0) {
            $this->command?->warn('No se encontraron clientes en la base legacy (chicheria).');
            return;
        }

        $this->command?->info("Iniciando migracion de {$total} clientes...");

        $migrados = 0;

        $legacy->chunkById(500, function ($rows) use (&$migrados) {
            $payload = [];

            foreach ($rows as $row) {
                $nombre = $this->pickNombre($row);
                $tipoCliente = $this->mapTipoCliente($row);
                $estado = strtoupper((string) ($row->estado ?? 'ACTIVO')) === 'ACTIVO';

                $extras = $this->buildExtras($row);
                $observacion = trim((string) ($row->observacion ?? ''));
                $observacion2 = $extras !== '' ? $extras : null;

                $payload[] = [
                    'id' => (int) $row->id,
                    'nombre' => $nombre,
                    'tipo_cliente' => $tipoCliente,
                    'ci' => $this->nullableTrim($row->ci),
                    'telefono' => $this->nullableTrim($row->telefono),
                    'direccion' => $this->nullableTrim($row->direccion),
                    'observacion' => $observacion !== '' ? $observacion : null,
                    'observacion_2' => $observacion2,
                    'lat' => null,
                    'lng' => null,
                    'estado' => $estado,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                    'deleted_at' => null,
                ];
            }

            DB::table('clientes')->upsert(
                $payload,
                ['id'],
                [
                    'nombre',
                    'tipo_cliente',
                    'ci',
                    'telefono',
                    'direccion',
                    'observacion',
                    'observacion_2',
                    'lat',
                    'lng',
                    'estado',
                    'updated_at',
                ]
            );

            $migrados += count($payload);
            $this->command?->info("Migrados {$migrados} clientes...");
        });

        $this->command?->info("Migracion finalizada. Total procesado: {$migrados}.");
    }

    private function pickNombre(object $row): string
    {
        $options = [
            $this->nullableTrim($row->titular ?? null),
            $this->nullableTrim($row->local ?? null),
            $this->nullableTrim($row->razon ?? null),
        ];

        foreach ($options as $value) {
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return 'Cliente ' . (int) $row->id;
    }

    private function mapTipoCliente(object $row): string
    {
        $tipoLegacyRaw = trim((string) ($row->tipocliente ?? ''));
        if ($tipoLegacyRaw === '1') {
            return 'detalle';
        }
        if ($tipoLegacyRaw === '2') {
            return 'local';
        }

        $tipoTexto = strtolower((string) ($row->tipo ?? ''));
        if (str_contains($tipoTexto, 'local')) {
            return 'local';
        }
        if (str_contains($tipoTexto, 'detalle')) {
            return 'detalle';
        }

        $tipoLegacy = (int) ($row->tipocliente ?? 1);
        return $tipoLegacy === 0 ? 'local' : 'detalle';
    }

    private function buildExtras(object $row): string
    {
        $parts = [];

        $this->pushExtra($parts, 'local', $row->local ?? null);
        $this->pushExtra($parts, 'titular', $row->titular ?? null);
        $this->pushExtra($parts, 'tipo', $row->tipo ?? null);
        $this->pushExtra($parts, 'fechanac', $row->fechanac ?? null);
        $this->pushExtra($parts, 'legalidad', $row->legalidad ?? null);
        $this->pushExtra($parts, 'categoria', $row->categoria ?? null);
        $this->pushExtra($parts, 'razon', $row->razon ?? null);
        $this->pushExtra($parts, 'nit', $row->nit ?? null);

        return implode(' | ', $parts);
    }

    private function pushExtra(array &$parts, string $key, mixed $value): void
    {
        $value = $this->nullableTrim($value);
        if ($value !== null && $value !== '') {
            $parts[] = "{$key}: {$value}";
        }
    }

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }
}
