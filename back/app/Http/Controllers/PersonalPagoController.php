<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Personal;
use App\Models\PersonalPago;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PersonalPagoController extends Controller
{
    public function index(Request $request)
    {
        $query = PersonalPago::with(['personal', 'caja', 'user'])->orderBy('id', 'desc');
        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }
        if ($request->filled('personal_id')) {
            $query->where('personal_id', $request->personal_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_registro')) {
            $query->where('tipo_registro', $request->tipo_registro);
        }
        return $query->get();
    }

    public function resumenMensual(Request $request)
    {
        $query = PersonalPago::with(['personal'])
            ->where('estado', 'ACTIVO')
            ->whereHas('personal', function ($q) {
                $q->where('estado', 'ACTIVO');
            });

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }
        if ($request->filled('personal_id')) {
            $query->where('personal_id', $request->personal_id);
        }

        $rows = $query->get();
        $grouped = $rows->groupBy(function ($r) {
            return $r->personal_id . '|' . $r->mes;
        });

        $result = $grouped->map(function ($items) {
            $first = $items->first();
            $sueldo = (float) $items->where('tipo_registro', 'salario')->sum('sueldo');
            $extras = (float) $items->where('tipo_registro', 'extra')->sum('extras');
            $adelantos = (float) $items->where('tipo_registro', 'adelanto')->sum('adelantos');
            $descuentos = (float) $items->where('tipo_registro', 'descuento')->sum('descuentos');
            $pagado = (float) $items->where('tipo_registro', 'salario')->sum('monto_pagado');
            return [
                'personal_id' => $first->personal_id,
                'personal_nombre' => $first->personal?->nombre,
                'ci' => $first->personal?->ci,
                'mes' => $first->mes,
                'sueldo' => round($sueldo, 2),
                'extras' => round($extras, 2),
                'adelantos' => round($adelantos, 2),
                'descuentos' => round($descuentos, 2),
                'total_calculado' => round(max(0, $sueldo + $extras - $adelantos - $descuentos), 2),
                'total_pagado_salario' => round($pagado, 2),
            ];
        })->values();

        return $result;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'personal_id' => 'required|exists:personales,id',
            'caja_id' => 'nullable|exists:cajas,id',
            'mes' => ['required', 'date_format:Y-m'],
            'tipo_registro' => ['required', Rule::in(['salario', 'extra', 'adelanto', 'descuento'])],
            'monto' => 'nullable|numeric|min:0.01',
            'sueldo' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:1000',
            'fecha_pago' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $personal = Personal::findOrFail($validated['personal_id']);
            $tipo = $validated['tipo_registro'];
            $cajaId = isset($validated['caja_id']) ? (int) $validated['caja_id'] : null;
            if (in_array($tipo, ['salario', 'adelanto'], true) && !$cajaId) {
                throw ValidationException::withMessages([
                    'caja_id' => 'Debe seleccionar caja para este tipo de registro',
                ]);
            }
            $montoInput = (float) ($validated['monto'] ?? 0);
            $sueldo = 0.0;
            $extras = 0.0;
            $adelantos = 0.0;
            $descuentos = 0.0;
            $monto = 0.0;
            $venta = null;

            if ($tipo === 'salario') {
                $yaPagado = PersonalPago::where('personal_id', $personal->id)
                    ->where('mes', $validated['mes'])
                    ->where('tipo_registro', 'salario')
                    ->where('estado', 'ACTIVO')
                    ->exists();
                if ($yaPagado) {
                    return response()->json(['message' => 'No puede pagar salario mensual dos veces en el mismo mes'], 422);
                }

                $sueldo = (float) ($validated['sueldo'] ?? $personal->salario ?? 0);
                $extraMes = (float) PersonalPago::where('personal_id', $personal->id)->where('mes', $validated['mes'])
                    ->where('estado', 'ACTIVO')->where('tipo_registro', 'extra')->sum('extras');
                $adelantoMes = (float) PersonalPago::where('personal_id', $personal->id)->where('mes', $validated['mes'])
                    ->where('estado', 'ACTIVO')->where('tipo_registro', 'adelanto')->sum('adelantos');
                $descuentoMes = (float) PersonalPago::where('personal_id', $personal->id)->where('mes', $validated['mes'])
                    ->where('estado', 'ACTIVO')->where('tipo_registro', 'descuento')->sum('descuentos');

                $extras = $extraMes;
                $adelantos = $adelantoMes;
                $descuentos = $descuentoMes;
                $monto = round(max(0, $sueldo + $extras - $adelantos - $descuentos), 2);
                if ($monto <= 0) {
                    return response()->json(['message' => 'El monto final de salario debe ser mayor a 0'], 422);
                }
                $saldoDisponible = $this->saldoCajaDisponible($cajaId);
                if ($monto > $saldoDisponible) {
                    throw ValidationException::withMessages([
                        'monto' => 'Fondos insuficientes en caja. Disponible: ' . round($saldoDisponible, 2) . ' Bs',
                    ]);
                }
                $venta = $this->crearMovimientoCaja(
                    $cajaId,
                    'egreso',
                    $monto,
                    'Pago salario: ' . $personal->nombre . ' - Mes ' . $validated['mes'],
                    $request->user()->id ?? null
                );
            } elseif ($tipo === 'extra') {
                $extras = round($montoInput, 2);
                $monto = $extras;
            } elseif ($tipo === 'adelanto') {
                $adelantos = round($montoInput, 2);
                $monto = $adelantos;
                $saldoDisponible = $this->saldoCajaDisponible($cajaId);
                if ($monto > $saldoDisponible) {
                    throw ValidationException::withMessages([
                        'monto' => 'Fondos insuficientes en caja. Disponible: ' . round($saldoDisponible, 2) . ' Bs',
                    ]);
                }
                $venta = $this->crearMovimientoCaja(
                    $cajaId,
                    'egreso',
                    $monto,
                    'Adelanto: ' . $personal->nombre . ' - Mes ' . $validated['mes'],
                    $request->user()->id ?? null
                );
            } else { // descuento
                $descuentos = round($montoInput, 2);
                $monto = $descuentos;
            }

            $pago = PersonalPago::create([
                'personal_id' => $personal->id,
                'caja_id' => in_array($tipo, ['salario', 'adelanto'], true) ? $cajaId : null,
                'user_id' => $request->user()->id ?? null,
                'venta_id' => $venta?->id,
                'mes' => $validated['mes'],
                'tipo_registro' => $tipo,
                'sueldo' => round($sueldo, 2),
                'extras' => round($extras, 2),
                'adelantos' => round($adelantos, 2),
                'descuentos' => round($descuentos, 2),
                'monto_pagado' => $monto,
                'estado' => 'ACTIVO',
                'observacion' => $validated['observacion'] ?? null,
                'fecha_pago' => $validated['fecha_pago'] ?? now()->toDateString(),
            ]);

            return $pago->load(['personal', 'caja', 'user']);
        });
    }

    public function boletaPdf(PersonalPago $personalPago)
    {
        if ($personalPago->tipo_registro !== 'salario') {
            return response()->json(['message' => 'Solo aplica para pagos de salario'], 422);
        }

        $personalPago->load(['personal', 'caja', 'user']);
        $mes = $personalPago->mes;
        [$y, $m] = explode('-', $mes);
        $ini = "{$y}-{$m}-01";
        $fin = date('Y-m-t', strtotime($ini));

        $pdf = Pdf::loadView('pdf.personal_boleta', [
            'pago' => $personalPago,
            'periodo_inicio' => $ini,
            'periodo_fin' => $fin,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('boleta-personal-' . $personalPago->id . '.pdf');
    }

    public function anular(Request $request, PersonalPago $personalPago)
    {
        if ($personalPago->estado === 'ANULADO') {
            return response()->json(['message' => 'El pago ya fue anulado'], 422);
        }

        return DB::transaction(function () use ($personalPago) {
            if (!empty($personalPago->venta_id)) {
                Venta::where('id', $personalPago->venta_id)->update(['estado' => 'ANULADA']);
            }

            // Compatibilidad con datos anteriores: si existia una venta de reversion,
            // tambien se anula para que no impacte totales de caja.
            if (!empty($personalPago->venta_reversion_id)) {
                Venta::where('id', $personalPago->venta_reversion_id)->update(['estado' => 'ANULADA']);
            }

            $personalPago->update([
                'estado' => 'ANULADO',
                'venta_reversion_id' => null,
            ]);

            return $personalPago->load(['personal', 'caja', 'user']);
        });
    }

    private function crearMovimientoCaja(int $cajaId, string $tipo, float $monto, string $obs, ?int $userId): Venta
    {
        $venta = Venta::create([
            'caja_id' => $cajaId,
            'cliente_id' => null,
            'user_id' => $userId,
            'tipo_venta' => 'caja',
            'tipo_movimiento' => $tipo,
            'tipo_pago' => 'contado',
            'estado' => 'ACTIVA',
            'cliente_nombre' => null,
            'cliente_telefono' => null,
            'cliente_direccion' => null,
            'total' => round($monto, 2),
            'observacion' => $obs,
        ]);

        Pago::create([
            'venta_id' => $venta->id,
            'user_id' => $userId,
            'nro_cuota' => 1,
            'monto' => round($monto, 2),
            'fecha_programada' => now()->toDateString(),
            'fecha_pago' => now(),
            'metodo' => 'efectivo',
            'estado' => 'PAGADO',
            'observacion' => 'Pago personal',
        ]);

        return $venta;
    }

    private function saldoCajaDisponible(int $cajaId): float
    {
        $pagos = DB::table('pagos')
            ->selectRaw('venta_id, SUM(monto) as pagado')
            ->where('estado', 'PAGADO')
            ->groupBy('venta_id');

        $row = DB::table('ventas as v')
            ->leftJoinSub($pagos, 'pg', function ($join) {
                $join->on('pg.venta_id', '=', 'v.id');
            })
            ->whereNull('v.deleted_at')
            ->where('v.estado', 'ACTIVA')
            ->where('v.caja_id', $cajaId)
            ->selectRaw("
                SUM(
                    CASE WHEN v.tipo_movimiento='ingreso'
                        THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                        ELSE 0 END
                ) as ingresos,
                SUM(
                    CASE WHEN v.tipo_movimiento='egreso'
                        THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                        ELSE 0 END
                ) as egresos
            ")
            ->first();

        $ingresos = (float) ($row->ingresos ?? 0);
        $egresos = (float) ($row->egresos ?? 0);
        return round($ingresos - $egresos, 2);
    }
}
