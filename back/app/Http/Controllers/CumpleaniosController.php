<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Personal;
use App\Models\PersonalPago;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CumpleaniosController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $month = (int) $request->get('month', 0);
        $includePastDays = (int) $request->get('include_past_days', 30);
        $includeNextDays = (int) $request->get('include_next_days', 60);
        $today = now()->startOfDay();

        $items = collect()
            ->merge($this->buildPersonalItems($today))
            ->merge($this->buildClienteItems($today))
            ->filter(function ($row) use ($search, $month, $includePastDays, $includeNextDays) {
                if ($search !== '') {
                    $haystack = strtoupper(
                        trim(($row['nombre'] ?? '') . ' ' . ($row['ci'] ?? '') . ' ' . ($row['telefono'] ?? ''))
                    );
                    if (!str_contains($haystack, strtoupper($search))) {
                        return false;
                    }
                }

                if ($month > 0 && (int) ($row['mes_cumple'] ?? 0) !== $month) {
                    return false;
                }

                $dias = (int) ($row['dias_cumple'] ?? 0);
                if ($dias < 0 && abs($dias) > $includePastDays) {
                    return false;
                }
                if ($dias > 0 && $dias > $includeNextDays) {
                    return false;
                }

                return true;
            })
            ->sort(function ($a, $b) {
                $aDias = (int) ($a['dias_cumple'] ?? 0);
                $bDias = (int) ($b['dias_cumple'] ?? 0);
                if ($aDias === $bDias) {
                    return strcmp((string) $a['nombre'], (string) $b['nombre']);
                }
                return abs($aDias) <=> abs($bDias);
            })
            ->values();

        return response()->json($items);
    }

    public function historial(string $origen, int $id)
    {
        if (!in_array($origen, ['cliente', 'personal'], true)) {
            return response()->json(['message' => 'Origen invalido'], 422);
        }

        if ($origen === 'cliente') {
            $cliente = Cliente::findOrFail($id);
            $ventas = Venta::with(['detalles', 'pagos', 'user'])
                ->where('cliente_id', $cliente->id)
                ->orderBy('id', 'desc')
                ->limit(25)
                ->get()
                ->map(function (Venta $v) {
                    $pagado = $v->pagos->where('estado', 'PAGADO')->sum('monto');
                    $deuda = $v->pagos->where('estado', 'PENDIENTE')->sum('monto');
                    $v->setAttribute('total_pagado', round((float) $pagado, 2));
                    $v->setAttribute('saldo_pendiente', round((float) $deuda, 2));
                    return $v;
                });

            return response()->json([
                'origen' => 'cliente',
                'entity' => $cliente,
                'historial' => $ventas->values(),
            ]);
        }

        $personal = Personal::findOrFail($id);
        $pagos = PersonalPago::with(['caja', 'user'])
            ->where('personal_id', $personal->id)
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();

        return response()->json([
            'origen' => 'personal',
            'entity' => $personal,
            'historial' => $pagos,
        ]);
    }

    private function buildPersonalItems(Carbon $today)
    {
        return Personal::query()
            ->where('estado', 'ACTIVO')
            ->whereNotNull('fechanac')
            ->get()
            ->map(fn (Personal $p) => $this->mapBirthdayRow(
                'personal',
                (int) $p->id,
                (string) $p->nombre,
                (string) ($p->ci ?? ''),
                (string) ($p->celular ?? ''),
                (string) $p->fechanac,
                $today
            ))
            ->filter()
            ->values();
    }

    private function buildClienteItems(Carbon $today)
    {
        return Cliente::query()
            ->where('estado', true)
            ->get()
            ->map(function (Cliente $c) use ($today) {
                $fecha = $c->fechanac ? (string) $c->fechanac : $this->extractLegacyFechaNac((string) ($c->observacion_2 ?? ''));
                return $this->mapBirthdayRow(
                    'cliente',
                    (int) $c->id,
                    (string) $c->nombre,
                    (string) ($c->ci ?? ''),
                    (string) ($c->telefono ?? ''),
                    (string) $fecha,
                    $today,
                    (string) ($c->tipo_cliente ?? 'detalle')
                );
            })
            ->filter()
            ->values();
    }

    private function mapBirthdayRow(
        string $origen,
        int $id,
        string $nombre,
        string $ci,
        string $telefono,
        string $fechanac,
        Carbon $today,
        string $tipoCliente = ''
    ): ?array {
        $fechanac = trim($fechanac);
        if ($fechanac === '') {
            return null;
        }

        try {
            $birth = Carbon::parse($fechanac);
        } catch (\Throwable $e) {
            return null;
        }

        $year = (int) $today->year;
        $month = (int) $birth->month;
        $day = (int) $birth->day;
        $thisYearBirthday = $this->safeBirthdayDate($year, $month, $day);
        $diasCumple = $today->diffInDays($thisYearBirthday, false);

        $estado = 'proximo';
        if ($diasCumple === 0) {
            $estado = 'hoy';
        } elseif ($diasCumple < 0) {
            $estado = 'pasado';
        }

        $mensaje = $estado === 'hoy'
            ? "Hoy es el cumpleaños de {$nombre}. ¡Felicidades de parte del sistema Naty!"
            : ($estado === 'proximo'
                ? "Hola {$nombre}, faltan {$diasCumple} dias para tu cumpleaños. Te saluda Chicheria Naty."
                : "Hola {$nombre}, feliz cumpleaños atrasado. Te saluda Chicheria Naty.");

        return [
            'origen' => $origen,
            'id' => $id,
            'nombre' => $nombre,
            'ci' => $ci,
            'telefono' => $telefono,
            'tipo_cliente' => $tipoCliente !== '' ? $tipoCliente : null,
            'fechanac' => $birth->toDateString(),
            'mes_cumple' => $month,
            'dia_cumple' => $day,
            'dias_cumple' => $diasCumple,
            'estado_cumple' => $estado,
            'mensaje_whatsapp' => $mensaje,
            'whatsapp_url' => $this->buildWhatsappUrl($telefono, $mensaje),
        ];
    }

    private function safeBirthdayDate(int $year, int $month, int $day): Carbon
    {
        if ($month === 2 && $day === 29 && !Carbon::create($year, 1, 1)->isLeapYear()) {
            return Carbon::create($year, 2, 28)->startOfDay();
        }

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    private function extractLegacyFechaNac(string $obs2): ?string
    {
        if ($obs2 === '') {
            return null;
        }
        if (preg_match('/fechanac:\s*([0-9]{4}-[0-9]{2}-[0-9]{2})/i', $obs2, $m) === 1) {
            return $m[1];
        }
        return null;
    }

    private function buildWhatsappUrl(string $telefono, string $mensaje): ?string
    {
        $digits = preg_replace('/\D+/', '', $telefono);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) <= 8) {
            $digits = '591' . $digits;
        }
        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($mensaje);
    }
}

