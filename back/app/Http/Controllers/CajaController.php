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

class CajaController extends Controller
{
    public function index()
    {
        return Caja::where('estado', true)->orderBy('id')->get();
    }

    public function resumen(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();

        $cajas = Caja::where('estado', true)->orderBy('id')->get();

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

        $totIngresos = (float) $movimientos->where('tipo_movimiento', 'ingreso')->sum('monto_real');
        $totEgresos = (float) $movimientos->where('tipo_movimiento', 'egreso')->sum('monto_real');

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
            ->groupBy('venta_id');

        return DB::table('ventas as v')
            ->leftJoinSub($pagos, 'pg', function ($join) {
                $join->on('pg.venta_id', '=', 'v.id');
            })
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->whereNull('v.deleted_at')
            ->where('v.estado', 'ACTIVA');
    }

    private function sumMovimientos(int $cajaId, string $tipo, Carbon $start, Carbon $end): float
    {
        $row = $this->movimientosQuery()
            ->where('v.caja_id', $cajaId)
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
}
