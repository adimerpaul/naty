<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Pago;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CajaController extends Controller
{
    private const CAJA_PRESTAMOS_ID = 3;

    public function index()
    {
        return Caja::where('estado', true)
            ->where('id', '!=', self::CAJA_PRESTAMOS_ID)
            ->orderBy('id')
            ->get();
    }

    public function resumen(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();

        $cajas = Caja::where('estado', true)
            ->where('id', '!=', self::CAJA_PRESTAMOS_ID)
            ->orderBy('id')
            ->get();

        $data = $cajas->map(function (Caja $caja) use ($start, $end) {
            return [
                'id' => $caja->id,
                'nombre' => $caja->nombre,
                'descripcion' => $caja->descripcion,
                'ingresos_mes' => $this->sumMovimientos($caja->id, 'ingreso', $start, $end),
                'egresos_mes' => $this->sumMovimientos($caja->id, 'egreso', $start, $end),
                'saldo_actual' => $this->saldoCaja($caja->id),
            ];
        });

        return response()->json([
            'month' => $month,
            'cajas' => $data,
        ]);
    }

    public function movimientos(Request $request, Caja $caja)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $date = $request->get('date');
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();

        $query = $this->movimientosQuery()
            ->where('v.caja_id', $caja->id)
            ->whereBetween('v.created_at', [$start, $end]);

        if ($date) {
            $query->whereDate('v.created_at', $date);
        }

        $movimientos = $query
            ->selectRaw("
                v.id,
                v.created_at,
                v.tipo_movimiento,
                v.tipo_venta,
                v.observacion,
                v.estado,
                v.cliente_nombre,
                COALESCE(u.name, u.username, '-') as usuario,
                CASE
                    WHEN v.tipo_pago = 'credito' THEN COALESCE(pg.pagado, 0)
                    ELSE COALESCE(pg.pagado, v.total)
                END as monto_real
            ")
            ->orderBy('v.created_at', 'desc')
            ->get();

        $diarioRaw = $this->movimientosQuery()
            ->where('v.caja_id', $caja->id)
            ->where('v.estado', 'ACTIVA')
            ->whereBetween('v.created_at', [$start, $end])
            ->selectRaw("
                DATE(v.created_at) as fecha,
                SUM(CASE WHEN v.tipo_movimiento='ingreso'
                    THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                    ELSE 0 END) as ingresos,
                SUM(CASE WHEN v.tipo_movimiento='egreso'
                    THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                    ELSE 0 END) as egresos
            ")
            ->groupByRaw('DATE(v.created_at)')
            ->orderByRaw('DATE(v.created_at) ASC')
            ->get()
            ->keyBy('fecha');

        $diario = collect();
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $f = $cursor->toDateString();
            $row = $diarioRaw->get($f);
            $ing = (float) ($row->ingresos ?? 0);
            $egr = (float) ($row->egresos ?? 0);
            $diario->push([
                'fecha' => $f,
                'ingresos' => round($ing, 2),
                'egresos' => round($egr, 2),
                'neto' => round($ing - $egr, 2),
            ]);
            $cursor->addDay();
        }

        $totIngresos = (float) $movimientos->where('estado', 'ACTIVA')->where('tipo_movimiento', 'ingreso')->sum('monto_real');
        $totEgresos = (float) $movimientos->where('estado', 'ACTIVA')->where('tipo_movimiento', 'egreso')->sum('monto_real');

        return response()->json([
            'caja' => $caja,
            'month' => $month,
            'date' => $date,
            'movimientos' => $movimientos,
            'diario' => $diario,
            'totales' => [
                'ingresos' => round($totIngresos, 2),
                'egresos' => round($totEgresos, 2),
                'saldo' => round($totIngresos - $totEgresos, 2),
            ],
        ]);
    }

    public function resumenGastos(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $userId = $request->get('user_id');

        $startDt = $dateFrom . ' 00:00:00';
        $endDt   = $dateTo   . ' 23:59:59';

        // Ventas del periodo (tipo detalle o local)
        $ventasQuery = Venta::with(['detalles', 'user', 'pagos'])
            ->whereIn('tipo_venta', ['detalle', 'local'])
            ->where('estado', '!=', 'ANULADA')
            ->where(function ($q) use ($dateFrom, $dateTo, $startDt, $endDt) {
                $q->where(function ($q2) use ($dateFrom, $dateTo) {
                    $q2->whereNotNull('fecha_venta')
                        ->whereDate('fecha_venta', '>=', $dateFrom)
                        ->whereDate('fecha_venta', '<=', $dateTo);
                })->orWhere(function ($q2) use ($startDt, $endDt) {
                    $q2->whereNull('fecha_venta')
                        ->whereBetween('created_at', [$startDt, $endDt]);
                });
            })
            ->orderBy('id', 'desc');

        if ($userId) {
            $ventasQuery->where('user_id', $userId);
        }

        $ventas = $ventasQuery->get()->map(function (Venta $v) {
            $pagado = $v->pagos->where('estado', 'PAGADO')->sum('monto');
            $saldo  = $v->pagos->where('estado', 'PENDIENTE')->sum('monto');
            return [
                'id'             => $v->id,
                'fecha'          => $v->fecha_venta?->format('Y-m-d') ?: optional($v->created_at)->format('Y-m-d'),
                'hora'           => optional($v->created_at)->format('H:i:s'),
                'tipo_venta'     => $v->tipo_venta,
                'cliente_nombre' => $v->cliente_nombre,
                'total'          => (float) $v->total,
                'total_pagado'   => round((float) $pagado, 2),
                'saldo_pendiente'=> round((float) $saldo, 2),
                'estado'         => $v->estado,
                'observacion'    => $v->observacion,
                'user'           => $v->user ? ['id' => $v->user->id, 'name' => $v->user->name ?? $v->user->username] : null,
                'detalles'       => $v->detalles->map(fn ($d) => [
                    'id'              => $d->id,
                    'producto_nombre' => $d->producto_nombre,
                    'cantidad'        => (float) $d->cantidad,
                    'precio'          => (float) $d->precio,
                    'subtotal'        => (float) $d->subtotal,
                ])->values(),
            ];
        });

        // Movimientos de caja (egresos manuales) del periodo
        $pagos = DB::table('pagos')
            ->selectRaw('venta_id, SUM(monto) as pagado')
            ->where('estado', 'PAGADO')
            ->whereNull('deleted_at')
            ->groupBy('venta_id');

        $movimientosQuery = DB::table('ventas as v')
            ->leftJoinSub($pagos, 'pg', fn ($j) => $j->on('pg.venta_id', '=', 'v.id'))
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->whereNull('v.deleted_at')
            ->where('v.tipo_venta', 'caja')
            ->where('v.tipo_movimiento', 'egreso')
            ->where('v.estado', 'ACTIVA')
            ->whereBetween('v.created_at', [$startDt, $endDt])
            ->selectRaw("
                v.id,
                v.created_at,
                v.tipo_movimiento,
                v.observacion,
                v.estado,
                COALESCE(u.name, u.username, '-') as usuario,
                CASE
                    WHEN v.tipo_pago = 'credito' THEN COALESCE(pg.pagado, 0)
                    ELSE COALESCE(pg.pagado, v.total)
                END as monto_real
            ")
            ->orderBy('v.created_at', 'desc');

        if ($userId) {
            $movimientosQuery->where('v.user_id', $userId);
        }

        $movimientos = $movimientosQuery->get();

        // Pagos individuales realizados en el periodo (por fecha de pago, no de venta)
        $pagosQuery = DB::table('pagos as p')
            ->join('ventas as v', 'v.id', '=', 'p.venta_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->whereNull('p.deleted_at')
            ->where('p.estado', 'PAGADO')
            ->whereBetween('p.created_at', [$startDt, $endDt])
            ->whereIn('v.tipo_venta', ['detalle', 'local'])
            ->selectRaw("
                p.id,
                p.venta_id,
                p.monto,
                p.metodo,
                p.nro_cuota,
                p.observacion,
                p.created_at,
                v.tipo_venta,
                v.cliente_nombre,
                v.total as venta_total,
                COALESCE(u.name, u.username, '-') as usuario
            ")
            ->orderBy('p.created_at', 'desc');

        if ($userId) {
            $pagosQuery->where('p.user_id', $userId);
        }

        $pagos = $pagosQuery->get();

        $totalVentas  = $ventas->sum('total');
        $totalPagado  = $pagos->sum('monto');
        $totalSaldo   = $ventas->sum('saldo_pendiente');
        $totalGastos  = $movimientos->sum('monto_real');

        return response()->json([
            'ventas'      => $ventas->values(),
            'movimientos' => $movimientos->values(),
            'pagos'       => $pagos->values(),
            'totales'     => [
                'total_ventas'  => round((float) $totalVentas, 2),
                'total_pagado'  => round((float) $totalPagado, 2),
                'total_saldo'   => round((float) $totalSaldo, 2),
                'total_gastos'  => round((float) $totalGastos, 2),
            ],
        ]);
    }

    public function registrarMovimiento(Request $request)
    {
        $validated = $request->validate([
            'modo' => ['required', Rule::in(['manual', 'transferencia'])],
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:500',
            'tipo_movimiento' => ['nullable', Rule::in(['ingreso', 'egreso'])],
            'caja_id' => 'nullable|exists:cajas,id',
            'origen_caja_id' => 'nullable|exists:cajas,id',
            'destino_caja_id' => 'nullable|exists:cajas,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $monto = round((float) $validated['monto'], 2);
            $obs = trim((string) ($validated['observacion'] ?? ''));

            if ($validated['modo'] === 'manual') {
                $cajaId = (int) ($validated['caja_id'] ?? 0);
                if ($cajaId <= 0) {
                    return response()->json(['message' => 'Debe seleccionar caja'], 422);
                }
                $tipo = $validated['tipo_movimiento'] ?? 'ingreso';
                if ($tipo === 'egreso') {
                    $this->validarSaldoSuficiente($cajaId, $monto);
                }
                $venta = $this->crearVentaCaja([
                    'caja_id' => $cajaId,
                    'tipo_movimiento' => $tipo,
                    'monto' => $monto,
                    'observacion' => $obs !== '' ? $obs : ($tipo === 'ingreso' ? 'Ingreso manual de caja' : 'Egreso manual de caja'),
                    'user_id' => $request->user()->id ?? null,
                ]);
                return response()->json(['message' => 'Movimiento registrado', 'movimientos' => [$venta]]);
            }

            $origen = (int) ($validated['origen_caja_id'] ?? 0);
            $destino = (int) ($validated['destino_caja_id'] ?? 0);
            if ($origen <= 0 || $destino <= 0 || $origen === $destino) {
                return response()->json(['message' => 'Debe elegir origen y destino diferentes'], 422);
            }

            $ref = 'TRF-' . strtoupper(Str::random(8));
            $obsBase = $obs !== '' ? $obs . ' | ' : '';
            $cajaOrigen = Caja::findOrFail($origen);
            $cajaDestino = Caja::findOrFail($destino);
            $this->validarSaldoSuficiente($origen, $monto);

            $egreso = $this->crearVentaCaja([
                'caja_id' => $origen,
                'tipo_movimiento' => 'egreso',
                'monto' => $monto,
                'observacion' => "{$obsBase}Transferencia a {$cajaDestino->nombre}. Ref: {$ref}",
                'user_id' => $request->user()->id ?? null,
            ]);

            $ingreso = $this->crearVentaCaja([
                'caja_id' => $destino,
                'tipo_movimiento' => 'ingreso',
                'monto' => $monto,
                'observacion' => "{$obsBase}Transferencia desde {$cajaOrigen->nombre}. Ref: {$ref}",
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'message' => 'Transferencia registrada',
                'referencia' => $ref,
                'movimientos' => [$egreso, $ingreso],
            ]);
        });
    }

    private function crearVentaCaja(array $data): Venta
    {
        $venta = Venta::create([
            'caja_id' => $data['caja_id'],
            'cliente_id' => null,
            'user_id' => $data['user_id'] ?? null,
            'tipo_venta' => 'caja',
            'tipo_movimiento' => $data['tipo_movimiento'],
            'tipo_pago' => 'contado',
            'estado' => 'ACTIVA',
            'cliente_nombre' => null,
            'cliente_telefono' => null,
            'cliente_direccion' => null,
            'total' => $data['monto'],
            'observacion' => $data['observacion'] ?? null,
        ]);

        Pago::create([
            'venta_id' => $venta->id,
            'user_id' => $data['user_id'] ?? null,
            'nro_cuota' => 1,
            'monto' => $data['monto'],
            'fecha_programada' => now()->toDateString(),
            'fecha_pago' => now(),
            'metodo' => 'efectivo',
            'estado' => 'PAGADO',
            'observacion' => 'Movimiento de caja',
        ]);

        return $venta;
    }

    private function movimientosQuery()
    {
        $pagos = DB::table('pagos')
            ->selectRaw('venta_id, SUM(monto) as pagado')
            ->where('estado', 'PAGADO')
            ->whereNull('deleted_at')
            ->groupBy('venta_id');

        return DB::table('ventas as v')
            ->leftJoinSub($pagos, 'pg', function ($join) {
                $join->on('pg.venta_id', '=', 'v.id');
            })
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->whereNull('v.deleted_at');
    }

    private function sumMovimientos(int $cajaId, string $tipo, Carbon $start, Carbon $end): float
    {
        $row = $this->movimientosQuery()
            ->where('v.caja_id', $cajaId)
            ->where('v.estado', 'ACTIVA')
            ->where('v.tipo_movimiento', $tipo)
            ->whereBetween('v.created_at', [$start, $end])
            ->selectRaw("
                SUM(
                    CASE
                        WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0)
                        ELSE COALESCE(pg.pagado, v.total)
                    END
                ) as monto
            ")
            ->first();

        return round((float) ($row->monto ?? 0), 2);
    }

    private function saldoCaja(int $cajaId): float
    {
        $row = $this->movimientosQuery()
            ->where('v.caja_id', $cajaId)
            ->where('v.estado', 'ACTIVA')
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

        $ing = (float) ($row->ingresos ?? 0);
        $egr = (float) ($row->egresos ?? 0);
        return round($ing - $egr, 2);
    }

    private function validarSaldoSuficiente(int $cajaId, float $monto): void
    {
        $saldoDisponible = $this->saldoCaja($cajaId);

        if ($monto > $saldoDisponible) {
            throw ValidationException::withMessages([
                'monto' => 'Fondos insuficientes en caja. Disponible: ' . round($saldoDisponible, 2) . ' Bs',
            ]);
        }
    }
}
