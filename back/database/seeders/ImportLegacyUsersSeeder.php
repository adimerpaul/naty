<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLegacyUsersSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = DB::connection('chicheria')->table('users')->orderBy('id');
        $total = $legacy->count();

        if ($total === 0) {
            $this->command?->warn('No se encontraron usuarios en la base legacy (chicheria).');
            return;
        }

        $this->command?->info("Iniciando migracion de {$total} usuarios...");

        $usernamesExistentes = DB::table('users')->pluck('username')->filter()->map(fn ($u) => strtoupper((string) $u))->toArray();
        $usernamesMap = array_fill_keys($usernamesExistentes, true);
        $migrados = 0;

        $legacy->chunkById(200, function ($rows) use (&$migrados, &$usernamesMap) {
            $payload = [];

            foreach ($rows as $row) {
                $email = trim((string) ($row->email ?? ''));
                $baseUsername = $email !== '' ? explode('@', $email)[0] : ('user' . (int) $row->id);
                $baseUsername = trim((string) $baseUsername);
                if ($baseUsername === '') {
                    $baseUsername = 'user' . (int) $row->id;
                }

                $username = $this->uniqueUsername($baseUsername, $usernamesMap);
                $usernamesMap[strtoupper($username)] = true;

                $payload[] = [
                    'id' => (int) $row->id,
                    'name' => trim((string) ($row->name ?? '')) !== '' ? trim((string) $row->name) : $username,
                    'username' => $username,
                    'email' => $email !== '' ? $email : null,
                    'password' => (string) $row->password, // hash legacy
                    'clave' => null,
                    'role' => 'Usuario',
                    'estado' => trim((string) ($row->estado ?? 'ACTIVO')) !== '' ? strtoupper((string) $row->estado) : 'ACTIVO',
                    'fechalimite' => trim((string) ($row->fechalimite ?? '')) !== '' ? (string) $row->fechalimite : null,
                    'avatar' => 'default.png',
                    'email_verified_at' => $row->email_verified_at,
                    'remember_token' => $row->remember_token,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                    'deleted_at' => null,
                ];
            }

            DB::table('users')->upsert(
                $payload,
                ['id'],
                [
                    'name',
                    'username',
                    'email',
                    'password',
                    'role',
                    'estado',
                    'fechalimite',
                    'updated_at',
                ]
            );

            $migrados += count($payload);
            $this->command?->info("Migrados {$migrados} usuarios...");
        });

        $this->command?->info("Migracion de usuarios finalizada. Total procesado: {$migrados}.");
    }

    private function uniqueUsername(string $base, array $map): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_.-]/', '', $base);
        if ($clean === '' || $clean === null) {
            $clean = 'user';
        }
        $clean = Str::limit($clean, 60, '');
        $candidate = $clean;
        $idx = 1;
        while (isset($map[strtoupper($candidate)])) {
            $candidate = Str::limit($clean, 55, '') . $idx;
            $idx++;
        }
        return $candidate;
    }
}

