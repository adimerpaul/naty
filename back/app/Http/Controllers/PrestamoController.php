<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\PrestamoRetorno;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrestamoController extends Controller
{
    private const CAJA_PRESTAMOS_ID = 3;

    public function index(Request $request)
    {
        $query = Prestamo::with(['cliente', 'inventario', 'venta', 'retornos.user'])->orderBy('id', 'desc');
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('tipo_venta')) {
            $tipoVenta = $request->validate([
                'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
            ])['tipo_venta'] ?? null;

            if ($tipoVenta) {
                $query->whereHas('cliente', fn ($q) => $q->where('tipo_cliente', $tipoVenta));
            }
        }

        return $query->get()->map(fn (Prestamo $prestamo) => $this->withResumen($prestamo))->values();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'tipo' => ['required', Rule::in(['prestamo', 'venta'])],
            'cliente_id' => 'required|exists:clientes,id',
            'inventario_id' => 'required|exists:inventarios,id',
            'venta_id' => 'nullable|exists:ventas,id',
            'cantidad' => 'required|integer|min:1',
            'efectivo' => 'nullable|numeric|min:0',
            'fisico' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:255',
            'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
            'metodo_pago' => ['nullable', Rule::in(['efectivo', 'qr'])],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $cliente = Cliente::findOrFail($validated['cliente_id']);
            if (! empty($validated['tipo_venta']) && $cliente->tipo_cliente !== $validated['tipo_venta']) {
                throw ValidationException::withMessages([
                    'cliente_id' => 'El cliente no corresponde al tipo de prestamo seleccionado.',
                ]);
            }

            $inv = Inventario::lockForUpdate()->findOrFail($validated['inventario_id']);
            if ($inv->cantidad < (int) $validated['cantidad']) {
                return response()->json(['message' => 'No hay cantidad suficiente en inventario'], 422);
            }

            $inv->update(['cantidad' => $inv->cantidad - (int) $validated['cantidad']]);

            $ventaGenerada = null;
            $montoPrestamo = $this->montoFisicoRecibido($validated);
            if ($validated['tipo'] === 'venta') {
                $monto = $montoPrestamo;
                if ($monto <= 0) {
                    return response()->json(['message' => 'Debe registrar efectivo para venta de material'], 422);
                }
                $ventaGenerada = Venta::create([
                    'caja_id' => 1,
                    'cliente_id' => $cliente?->id,
                    'user_id' => $request->user()->id ?? null,
                    'tipo_venta' => $validated['tipo_venta'] ?? 'detalle',
                    'tipo_movimiento' => 'ingreso',
                    'tipo_pago' => 'contado',
                    'estado' => 'ACTIVA',
                    'cliente_nombre' => $cliente?->nombre,
                    'cliente_telefono' => $cliente?->telefono,
                    'cliente_direccion' => $cliente?->direccion,
                    'observacion' => 'Venta de material inventario'
                        .(! empty($validated['tipo_venta']) ? ' '.$validated['tipo_venta'] : '')
                        .'. '.($validated['observacion'] ?? ''),
                    'total' => round($monto, 2),
                ]);
                VentaDetalle::create([
                    'venta_id' => $ventaGenerada->id,
                    'producto_id' => null,
                    'user_id' => $request->user()->id ?? null,
                    'producto_nombre' => "Material: {$inv->nombre}",
                    'precio' => round($monto / max((int) $validated['cantidad'], 1), 2),
                    'cantidad' => (int) $validated['cantidad'],
                    'subtotal' => round($monto, 2),
                    'estado' => true,
                ]);
                Pago::create([
                    'venta_id' => $ventaGenerada->id,
                    'user_id' => $request->user()->id ?? null,
                    'nro_cuota' => 1,
                    'monto' => round($monto, 2),
                    'fecha_programada' => now()->toDateString(),
                    'fecha_pago' => now(),
                    'metodo' => $validated['metodo_pago'] ?? 'efectivo',
                    'estado' => 'PAGADO',
                ]);
            }

            $prestamo = Prestamo::create([
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'tipo' => $validated['tipo'],
                'estado' => $validated['tipo'] === 'venta' ? 'VENDIDO' : 'EN PRESTAMO',
                'efectivo' => $montoPrestamo,
                'fisico' => $validated['fisico'] ?? '',
                'observacion' => $validated['observacion'] ?? '',
                'cantidad' => (int) $validated['cantidad'],
                'prestado' => $validated['tipo'] === 'venta',
                'user_id' => $request->user()->id ?? null,
                'cliente_id' => $validated['cliente_id'],
                'inventario_id' => $validated['inventario_id'],
                'venta_id' => $validated['venta_id'] ?? $ventaGenerada?->id,
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function retornar(Request $request, Prestamo $prestamo)
    {
        $prestamo->loadMissing('retornos');
        if (! in_array($prestamo->estado, ['EN PRESTAMO', 'PARCIAL'], true)) {
            return response()->json(['message' => 'Solo se puede retornar prestamos pendientes o parciales'], 422);
        }

        return DB::transaction(function () use ($prestamo, $request) {
            $prestamo->refresh()->load('retornos');
            $saldoCantidad = $this->saldoCantidad($prestamo);
            $saldoEfectivo = $this->saldoEfectivo($prestamo);

            if ($saldoCantidad <= 0 && $saldoEfectivo <= 0) {
                return response()->json(['message' => 'El prestamo ya fue retornado completamente'], 422);
            }

            $this->registrarRetorno($prestamo, $request, $saldoCantidad, $saldoEfectivo, null, 'Retorno completo');

            $prestamo->update([
                'estado' => 'RETORNADO',
                'prestado' => false,
                'observacion' => trim(($prestamo->observacion ? $prestamo->observacion.' | ' : '').'Retornado completo'),
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function retornoParcial(Request $request, Prestamo $prestamo)
    {
        $validated = $request->validate([
            'cantidad' => 'nullable|integer|min:0',
            'efectivo' => 'nullable|numeric|min:0',
            'fisico' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:255',
        ]);

        $prestamo->loadMissing('retornos');
        if (! in_array($prestamo->estado, ['EN PRESTAMO', 'PARCIAL'], true)) {
            return response()->json(['message' => 'Solo se puede retornar prestamos pendientes o parciales'], 422);
        }

        return DB::transaction(function () use ($prestamo, $request, $validated) {
            $prestamo->refresh()->load('retornos');
            $cantidad = (int) ($validated['cantidad'] ?? 0);
            $efectivo = $this->montoFisicoRecibido($validated);
            $fisico = trim((string) ($validated['fisico'] ?? ''));

            if ($cantidad <= 0 && $efectivo <= 0 && $fisico === '') {
                throw ValidationException::withMessages([
                    'cantidad' => 'Debe registrar cantidad o fisico retornado.',
                ]);
            }

            $saldoCantidad = $this->saldoCantidad($prestamo);
            $saldoEfectivo = $this->saldoEfectivo($prestamo);

            if ($cantidad > $saldoCantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad supera el saldo pendiente.',
                ]);
            }
            if ($efectivo > $saldoEfectivo) {
                throw ValidationException::withMessages([
                    'efectivo' => 'El fisico retornado supera el saldo pendiente.',
                ]);
            }

            $this->registrarRetorno(
                $prestamo,
                $request,
                $cantidad,
                $efectivo,
                $fisico,
                $validated['observacion'] ?? null
            );

            $prestamo->refresh()->load('retornos');
            $nuevoEstado = $this->saldoCantidad($prestamo) <= 0 && $this->saldoEfectivo($prestamo) <= 0
                ? 'RETORNADO'
                : 'PARCIAL';

            $prestamo->update([
                'estado' => $nuevoEstado,
                'prestado' => $nuevoEstado !== 'RETORNADO',
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function anular(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->tipo !== 'venta') {
            return response()->json(['message' => 'Solo se puede anular registros de tipo venta'], 422);
        }

        if ($prestamo->estado === 'ANULADO') {
            return response()->json(['message' => 'Este registro ya fue anulado'], 422);
        }

        return DB::transaction(function () use ($prestamo) {
            $prestamo->refresh()->loadMissing(['retornos', 'venta']);

            $cantidadRetornada = (int) ($prestamo->retornos?->sum('cantidad') ?? 0);
            $cantidadPendiente = max(0, (int) $prestamo->cantidad - $cantidadRetornada);

            if ($cantidadPendiente > 0) {
                $inv = Inventario::lockForUpdate()->findOrFail($prestamo->inventario_id);
                $inv->update(['cantidad' => (int) $inv->cantidad + $cantidadPendiente]);
            }

            $prestamo->update([
                'estado' => 'ANULADO',
                'prestado' => false,
                'observacion' => trim(($prestamo->observacion ? $prestamo->observacion . ' | ' : '') . 'Anulado por operador'),
            ]);

            if ($prestamo->venta_id && $prestamo->venta) {
                $venta = $prestamo->venta;
                if ($venta->estado !== 'ANULADA') {
                    $venta->update(['estado' => 'ANULADA']);
                    $venta->detalles()->update(['estado' => false]);
                }
            }

            return $this->withResumen($prestamo->fresh(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function darBaja(Request $request, Prestamo $prestamo)
    {
        $validated = $request->validate([
            'monto' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        if ($prestamo->tipo === 'venta') {
            return response()->json(['message' => 'No se puede dar de baja un registro de tipo venta'], 422);
        }

        if (in_array($prestamo->estado, ['RETORNADO', 'ANULADO', 'BAJA'], true)) {
            return response()->json(['message' => 'No se puede dar de baja este prestamo en su estado actual'], 422);
        }

        return DB::transaction(function () use ($prestamo, $request, $validated) {
            $prestamo->refresh()->loadMissing(['cliente', 'inventario', 'retornos']);
            $saldo = $this->saldoEfectivo($prestamo);
            $monto = isset($validated['monto']) ? round((float) $validated['monto'], 2) : $saldo;
            $obs = trim((string) ($validated['observacion'] ?? ''));

            $prestamo->update([
                'estado' => 'BAJA',
                'prestado' => false,
                'observacion' => trim(($prestamo->observacion ? $prestamo->observacion . ' | ' : '') . 'Baja: ' . ($obs !== '' ? $obs : 'sin detalle')),
            ]);

            if ($monto > 0) {
                $this->crearMovimientoCajaPrestamos([
                    'tipo_movimiento' => 'ingreso',
                    'monto' => $monto,
                    'user_id' => $request->user()->id ?? null,
                    'observacion' => 'Baja prestamo #' . $prestamo->id . ($obs !== '' ? ' - ' . $obs : ''),
                ]);
            }

            return $this->withResumen($prestamo->fresh(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function cajaResumen(Request $request)
    {
        $tipoVenta = $request->validate([
            'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
        ])['tipo_venta'] ?? 'detalle';

        $prestamos = Prestamo::with(['cliente', 'retornos'])
            ->whereHas('cliente', fn ($q) => $q->where('tipo_cliente', $tipoVenta))
            ->whereNotIn('estado', ['ANULADO'])
            ->get();

        $vendidosMonto = 0.0;
        $prestamosMonto = 0.0;
        $pendienteMonto = 0.0;
        foreach ($prestamos as $p) {
            $monto = $this->montoPrestamo($p);
            $pend = $this->saldoEfectivo($p);
            $pendienteMonto += $pend;
            if ($p->tipo === 'venta') {
                $vendidosMonto += $monto;
            } else {
                $prestamosMonto += $monto;
            }
        }

        return response()->json([
            'tipo_venta' => $tipoVenta,
            'vendidos_monto' => round($vendidosMonto, 2),
            'prestamos_monto' => round($prestamosMonto, 2),
            'pendiente_monto' => round($pendienteMonto, 2),
            'caja_total' => $this->saldoCajaPrestamos(),
        ]);
    }

    public function cajaMovimientos(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = Venta::with(['user', 'pagos'])
            ->where('caja_id', self::CAJA_PRESTAMOS_ID)
            ->where('tipo_venta', 'caja_prestamos')
            ->orderByDesc('id');

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }
        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $rows = $query->get()->map(function (Venta $v) {
            $pagado = (float) $v->pagos->where('estado', 'PAGADO')->sum('monto');
            return [
                'id' => $v->id,
                'created_at' => $v->created_at,
                'tipo_movimiento' => $v->tipo_movimiento,
                'monto' => round($pagado > 0 ? $pagado : (float) $v->total, 2),
                'observacion' => $v->observacion,
                'estado' => $v->estado,
                'usuario' => $v->user?->name ?? $v->user?->username ?? '-',
            ];
        })->values();

        return response()->json([
            'caja_total' => $this->saldoCajaPrestamos(),
            'movimientos' => $rows,
        ]);
    }

    public function reportePdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $validated = $request->validate([
            'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
            'tipo' => ['nullable', Rule::in(['prestamo', 'venta'])],
            'estado' => ['nullable', Rule::in(['VENDIDO', 'EN PRESTAMO', 'PARCIAL', 'RETORNADO', 'ANULADO', 'BAJA'])],
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $tipoVenta = $validated['tipo_venta'] ?? 'detalle';
        $query = Prestamo::with(['cliente', 'inventario', 'venta', 'retornos.user', 'user'])
            ->whereHas('cliente', fn ($q) => $q->where('tipo_cliente', $tipoVenta))
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if (! empty($validated['tipo'])) {
            $query->where('tipo', $validated['tipo']);
        }

        if (! empty($validated['estado'])) {
            $query->where('estado', $validated['estado']);
        }

        if (! empty($validated['date_from'])) {
            $query->whereDate('fecha', '>=', $validated['date_from']);
        }

        if (! empty($validated['date_to'])) {
            $query->whereDate('fecha', '<=', $validated['date_to']);
        }

        $prestamos = $query->get()->map(fn (Prestamo $prestamo) => $this->withResumen($prestamo));
        $resumen = $this->reporteResumen($prestamos);
        $porMaterial = $this->reportePorMaterial($prestamos);
        $porCliente = $this->reportePorCliente($prestamos);

        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.prestamos_reporte', [
            'prestamos' => $prestamos,
            'ventasMaterial' => $prestamos->where('tipo', 'venta')->values(),
            'prestamosMaterial' => $prestamos->where('tipo', 'prestamo')->values(),
            'porMaterial' => $porMaterial,
            'porCliente' => $porCliente,
            'resumen' => $resumen,
            'tipoVenta' => $tipoVenta,
            'tipoFiltro' => $validated['tipo'] ?? null,
            'estadoFiltro' => $validated['estado'] ?? null,
            'dateFrom' => $validated['date_from'] ?? null,
            'dateTo' => $validated['date_to'] ?? null,
            'usuario' => auth()->user()?->name ?? 'Sistema',
            'logo' => $logoBase64,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'dpi' => 96,
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        return $pdf->download('reporte-prestamos-' . $tipoVenta . '.pdf');
    }

    public function registrarCajaMovimiento(Request $request)
    {
        $validated = $request->validate([
            'tipo_movimiento' => ['required', Rule::in(['ingreso', 'egreso'])],
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $monto = round((float) $validated['monto'], 2);
            if ($validated['tipo_movimiento'] === 'egreso' && $monto > $this->saldoCajaPrestamos()) {
                return response()->json(['message' => 'Fondos insuficientes en caja prestamos'], 422);
            }

            $venta = $this->crearMovimientoCajaPrestamos([
                'tipo_movimiento' => $validated['tipo_movimiento'],
                'monto' => $monto,
                'user_id' => $request->user()->id ?? null,
                'observacion' => trim((string) ($validated['observacion'] ?? '')) ?: 'Movimiento manual caja prestamos',
            ]);

            return response()->json([
                'message' => 'Movimiento registrado',
                'movimiento' => $venta,
                'caja_total' => $this->saldoCajaPrestamos(),
            ]);
        });
    }

    private function registrarRetorno(
        Prestamo $prestamo,
        Request $request,
        int $cantidad,
        float $efectivo,
        ?string $fisico,
        ?string $observacion
    ): PrestamoRetorno {
        if ($cantidad > 0) {
            $inventario = Inventario::lockForUpdate()->findOrFail($prestamo->inventario_id);
            $inventario->update([
                'cantidad' => (int) $inventario->cantidad + $cantidad,
            ]);
        }

        return PrestamoRetorno::create([
            'prestamo_id' => $prestamo->id,
            'user_id' => $request->user()->id ?? null,
            'fecha' => now()->toDateString(),
            'cantidad' => $cantidad,
            'efectivo' => round($efectivo, 2),
            'fisico' => $fisico,
            'observacion' => $observacion,
        ]);
    }

    private function withResumen(Prestamo $prestamo): Prestamo
    {
        $prestamo->loadMissing('retornos');
        $retornadoCantidad = (int) $prestamo->retornos->sum('cantidad');
        $retornadoEfectivo = round((float) $prestamo->retornos->sum('efectivo'), 2);
        $fisicoRecibido = $this->montoPrestamo($prestamo);

        $prestamo->setAttribute('retornado_cantidad', $retornadoCantidad);
        $prestamo->setAttribute('cantidad_actual', max(0, (int) $prestamo->cantidad - $retornadoCantidad));
        $prestamo->setAttribute('retornado_efectivo', $retornadoEfectivo);
        $prestamo->setAttribute('efectivo_actual', max(0, round($fisicoRecibido - $retornadoEfectivo, 2)));
        $prestamo->setAttribute('fisico_recibido', $fisicoRecibido);
        $prestamo->setAttribute('monto_pendiente', max(0, round($fisicoRecibido - $retornadoEfectivo, 2)));

        return $prestamo;
    }

    private function saldoCantidad(Prestamo $prestamo): int
    {
        $prestamo->loadMissing('retornos');

        return max(0, (int) $prestamo->cantidad - (int) $prestamo->retornos->sum('cantidad'));
    }

    private function saldoEfectivo(Prestamo $prestamo): float
    {
        $prestamo->loadMissing('retornos');

        return max(0, round($this->montoPrestamo($prestamo) - (float) $prestamo->retornos->sum('efectivo'), 2));
    }

    private function montoFisicoRecibido(array $validated): float
    {
        $monto = round((float) ($validated['efectivo'] ?? 0), 2);
        if ($monto <= 0 && isset($validated['fisico']) && is_numeric($validated['fisico'])) {
            $monto = round((float) $validated['fisico'], 2);
        }

        return $monto;
    }

    private function montoPrestamo(Prestamo $prestamo): float
    {
        $monto = round((float) $prestamo->efectivo, 2);
        if ($monto <= 0 && is_numeric($prestamo->fisico)) {
            $monto = round((float) $prestamo->fisico, 2);
        }

        return $monto;
    }

    private function crearMovimientoCajaPrestamos(array $data): Venta
    {
        $venta = Venta::create([
            'caja_id' => self::CAJA_PRESTAMOS_ID,
            'cliente_id' => null,
            'user_id' => $data['user_id'] ?? null,
            'tipo_venta' => 'caja_prestamos',
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
            'observacion' => 'Movimiento caja prestamos',
        ]);

        return $venta;
    }

    private function saldoCajaPrestamos(): float
    {
        $rows = Venta::with('pagos')
            ->where('caja_id', self::CAJA_PRESTAMOS_ID)
            ->where('tipo_venta', 'caja_prestamos')
            ->where('estado', 'ACTIVA')
            ->get();

        $ing = 0.0;
        $egr = 0.0;
        foreach ($rows as $row) {
            $monto = (float) $row->pagos->where('estado', 'PAGADO')->sum('monto');
            $real = $monto > 0 ? $monto : (float) $row->total;
            if ($row->tipo_movimiento === 'egreso') {
                $egr += $real;
            } else {
                $ing += $real;
            }
        }

        return round($ing - $egr, 2);
    }

    private function reporteResumen($prestamos): array
    {
        return [
            'total_registros' => $prestamos->count(),
            'total_prestamos' => $prestamos->where('tipo', 'prestamo')->count(),
            'total_ventas' => $prestamos->where('tipo', 'venta')->count(),
            'cantidad_total' => (int) $prestamos->sum('cantidad'),
            'cantidad_pendiente' => (int) $prestamos->sum('cantidad_actual'),
            'monto_total' => round((float) $prestamos->sum('fisico_recibido'), 2),
            'monto_retornado' => round((float) $prestamos->sum('retornado_efectivo'), 2),
            'monto_pendiente' => round((float) $prestamos->sum('monto_pendiente'), 2),
            'vendido_monto' => round((float) $prestamos->where('tipo', 'venta')->sum('fisico_recibido'), 2),
            'prestado_monto' => round((float) $prestamos->where('tipo', 'prestamo')->sum('fisico_recibido'), 2),
            'en_prestamo' => $prestamos->whereIn('estado', ['EN PRESTAMO', 'PARCIAL'])->count(),
            'retornados' => $prestamos->where('estado', 'RETORNADO')->count(),
            'anulados' => $prestamos->where('estado', 'ANULADO')->count(),
            'bajas' => $prestamos->where('estado', 'BAJA')->count(),
            'caja_total' => $this->saldoCajaPrestamos(),
        ];
    }

    private function reportePorMaterial($prestamos)
    {
        return $prestamos
            ->groupBy(fn ($p) => $p->inventario?->nombre ?: 'Sin material')
            ->map(function ($rows, $material) {
                return [
                    'material' => $material,
                    'registros' => $rows->count(),
                    'cantidad' => (int) $rows->sum('cantidad'),
                    'pendiente' => (int) $rows->sum('cantidad_actual'),
                    'ventas' => $rows->where('tipo', 'venta')->count(),
                    'prestamos' => $rows->where('tipo', 'prestamo')->count(),
                    'monto' => round((float) $rows->sum('fisico_recibido'), 2),
                    'pendiente_monto' => round((float) $rows->sum('monto_pendiente'), 2),
                ];
            })
            ->sortBy('material')
            ->values();
    }

    private function reportePorCliente($prestamos)
    {
        return $prestamos
            ->groupBy(fn ($p) => $p->cliente?->nombre ?: 'Sin cliente')
            ->map(function ($rows, $cliente) {
                return [
                    'cliente' => $cliente,
                    'registros' => $rows->count(),
                    'cantidad' => (int) $rows->sum('cantidad'),
                    'pendiente' => (int) $rows->sum('cantidad_actual'),
                    'monto' => round((float) $rows->sum('fisico_recibido'), 2),
                    'pendiente_monto' => round((float) $rows->sum('monto_pendiente'), 2),
                ];
            })
            ->sortByDesc('pendiente_monto')
            ->values();
    }
}
