<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Prestamos</title>
    <style>
        @page { margin: 1.45cm 1.65cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #172033; background: #fff; }
        .header { display: table; width: 100%; border-bottom: 3px solid #2e7d32; padding-bottom: 12px; margin-bottom: 14px; }
        .header-logo { display: table-cell; width: 72px; vertical-align: middle; }
        .header-logo img { width: 58px; height: 58px; object-fit: contain; }
        .header-info { display: table-cell; vertical-align: middle; padding-left: 10px; }
        .empresa { font-size: 16px; font-weight: bold; color: #2e7d32; }
        .subtitulo { font-size: 11px; color: #444; margin-top: 2px; }
        .header-meta { display: table-cell; vertical-align: middle; text-align: right; color: #555; }
        .badge { display: inline-block; background: #2e7d32; color: #fff; padding: 2px 8px; border-radius: 9px; margin-top: 4px; }
        .summary { display: table; width: 100%; margin: 0 0 10px; }
        .metric { display: table-cell; width: 16.6%; border-left: 4px solid #2e7d32; background: #e8f5e9; padding: 8px 9px; }
        .metric.orange { border-color: #ef6c00; background: #fff3e0; }
        .metric.blue { border-color: #1565c0; background: #e3f2fd; }
        .metric.red { border-color: #c62828; background: #ffebee; }
        .gap { display: table-cell; width: 5px; }
        .metric .value { font-size: 13px; font-weight: bold; }
        .metric .label { font-size: 8px; color: #555; margin-top: 2px; }
        h2 { font-size: 11px; color: #1b5e20; margin: 12px 0 5px; border-bottom: 1px solid #c8e6c9; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #2e7d32; color: #fff; }
        thead th { padding: 6px 5px; font-size: 8px; text-align: left; }
        tbody td { padding: 5px; border-bottom: 1px solid #e5eaf0; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f7fbf7; }
        .right { text-align: right; }
        .center { text-align: center; }
        .estado { display: inline-block; padding: 1px 5px; border-radius: 7px; font-size: 7px; color: #fff; }
        .estado-prestamo { background: #ef6c00; }
        .estado-venta { background: #1565c0; }
        .estado-ok { background: #2e7d32; }
        .estado-bad { background: #c62828; }
        .small { color: #666; font-size: 7px; }
        .footer { margin-top: 12px; border-top: 1px solid #ddd; padding-top: 5px; color: #777; text-align: right; font-size: 8px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body style="padding: 20px">
<div class="header">
    <div class="header-logo">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo">
        @endif
    </div>
    <div class="header-info">
        <div class="empresa">Chicheria</div>
        <div class="subtitulo">Reporte completo de prestamos, ventas de material e inventario - {{ strtoupper($tipoVenta) }}</div>
        <div class="small">
            Periodo:
            {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Inicio' }}
            al
            {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'Actual' }}
            @if($tipoFiltro)
                | Tipo: {{ strtoupper($tipoFiltro) }}
            @endif
            @if($estadoFiltro)
                | Estado: {{ $estadoFiltro }}
            @endif
        </div>
    </div>
    <div class="header-meta">
        <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        <div><strong>Generado por:</strong> {{ $usuario }}</div>
        <div><strong>Registros:</strong> {{ $resumen['total_registros'] }}</div>
        <div><span class="badge">{{ strtoupper($tipoVenta) }}</span></div>
    </div>
</div>

<div class="summary">
    <div class="metric"><div class="value">{{ $resumen['total_registros'] }}</div><div class="label">Registros</div></div>
    <div class="gap"></div>
    <div class="metric blue"><div class="value">{{ $resumen['total_ventas'] }}</div><div class="label">Ventas material</div></div>
    <div class="gap"></div>
    <div class="metric orange"><div class="value">{{ $resumen['total_prestamos'] }}</div><div class="label">Prestamos material</div></div>
    <div class="gap"></div>
    <div class="metric"><div class="value">{{ number_format($resumen['monto_total'], 2) }}</div><div class="label">Monto total Bs</div></div>
    <div class="gap"></div>
    <div class="metric red"><div class="value">{{ number_format($resumen['monto_pendiente'], 2) }}</div><div class="label">Pendiente Bs</div></div>
    <div class="gap"></div>
    <div class="metric blue"><div class="value">{{ number_format($resumen['caja_total'], 2) }}</div><div class="label">Caja prestamos Bs</div></div>
</div>

<div class="summary">
    <div class="metric"><div class="value">{{ $resumen['cantidad_total'] }}</div><div class="label">Cantidad total</div></div>
    <div class="gap"></div>
    <div class="metric orange"><div class="value">{{ $resumen['cantidad_pendiente'] }}</div><div class="label">Cantidad pendiente</div></div>
    <div class="gap"></div>
    <div class="metric"><div class="value">{{ $resumen['en_prestamo'] }}</div><div class="label">En prestamo/parcial</div></div>
    <div class="gap"></div>
    <div class="metric blue"><div class="value">{{ $resumen['retornados'] }}</div><div class="label">Retornados</div></div>
    <div class="gap"></div>
    <div class="metric red"><div class="value">{{ $resumen['anulados'] }}</div><div class="label">Anulados</div></div>
    <div class="gap"></div>
    <div class="metric red"><div class="value">{{ $resumen['bajas'] }}</div><div class="label">Bajas</div></div>
</div>

<h2>Resumen por material</h2>
<table>
    <thead>
    <tr>
        <th>Material</th>
        <th class="right">Reg.</th>
        <th class="right">Cant.</th>
        <th class="right">Pend.</th>
        <th class="right">Ventas</th>
        <th class="right">Prestamos</th>
        <th class="right">Monto Bs</th>
        <th class="right">Pendiente Bs</th>
    </tr>
    </thead>
    <tbody>
    @forelse($porMaterial as $row)
        <tr>
            <td><strong>{{ $row['material'] }}</strong></td>
            <td class="right">{{ $row['registros'] }}</td>
            <td class="right">{{ $row['cantidad'] }}</td>
            <td class="right">{{ $row['pendiente'] }}</td>
            <td class="right">{{ $row['ventas'] }}</td>
            <td class="right">{{ $row['prestamos'] }}</td>
            <td class="right">{{ number_format($row['monto'], 2) }}</td>
            <td class="right">{{ number_format($row['pendiente_monto'], 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="center">Sin registros</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Resumen por cliente</h2>
<table>
    <thead>
    <tr>
        <th>Cliente</th>
        <th class="right">Registros</th>
        <th class="right">Cantidad</th>
        <th class="right">Pendiente</th>
        <th class="right">Monto Bs</th>
        <th class="right">Pendiente Bs</th>
    </tr>
    </thead>
    <tbody>
    @forelse($porCliente as $row)
        <tr>
            <td><strong>{{ $row['cliente'] }}</strong></td>
            <td class="right">{{ $row['registros'] }}</td>
            <td class="right">{{ $row['cantidad'] }}</td>
            <td class="right">{{ $row['pendiente'] }}</td>
            <td class="right">{{ number_format($row['monto'], 2) }}</td>
            <td class="right">{{ number_format($row['pendiente_monto'], 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="6" class="center">Sin registros</td></tr>
    @endforelse
    </tbody>
</table>

<h2 class="page-break">Listado completo</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Material</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th class="right">Cant.</th>
        <th class="right">Pend.</th>
        <th class="right">Monto</th>
        <th class="right">Retornado</th>
        <th class="right">Pend. Bs</th>
        <th>Usuario</th>
        <th>Observacion</th>
    </tr>
    </thead>
    <tbody>
    @forelse($prestamos as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->fecha ? \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') : '' }}</td>
            <td><strong>{{ $p->cliente?->nombre }}</strong><br><span class="small">{{ $p->cliente?->telefono }}</span></td>
            <td>{{ $p->inventario?->nombre }}</td>
            <td>
                <span class="estado {{ $p->tipo === 'venta' ? 'estado-venta' : 'estado-prestamo' }}">
                    {{ strtoupper($p->tipo) }}
                </span>
            </td>
            <td>
                <span class="estado {{ in_array($p->estado, ['ANULADO', 'BAJA'], true) ? 'estado-bad' : (in_array($p->estado, ['RETORNADO', 'VENDIDO'], true) ? 'estado-ok' : 'estado-prestamo') }}">
                    {{ $p->estado }}
                </span>
            </td>
            <td class="right">{{ $p->cantidad }}</td>
            <td class="right">{{ $p->cantidad_actual }}</td>
            <td class="right">{{ number_format($p->fisico_recibido, 2) }}</td>
            <td class="right">{{ number_format($p->retornado_efectivo, 2) }}</td>
            <td class="right">{{ number_format($p->monto_pendiente, 2) }}</td>
            <td>{{ $p->user?->name ?? $p->user?->username }}</td>
            <td>{{ $p->observacion }}</td>
        </tr>
    @empty
        <tr><td colspan="13" class="center">Sin registros</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Ventas de material</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Material</th>
        <th class="right">Cantidad</th>
        <th class="right">Monto Bs</th>
        <th>Observacion</th>
    </tr>
    </thead>
    <tbody>
    @forelse($ventasMaterial as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->fecha ? \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') : '' }}</td>
            <td>{{ $p->cliente?->nombre }}</td>
            <td>{{ $p->inventario?->nombre }}</td>
            <td class="right">{{ $p->cantidad }}</td>
            <td class="right">{{ number_format($p->fisico_recibido, 2) }}</td>
            <td>{{ $p->observacion }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="center">Sin ventas de material</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Prestamos y retornos</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Material</th>
        <th>Estado</th>
        <th class="right">Cant.</th>
        <th class="right">Ret.</th>
        <th class="right">Pend.</th>
        <th>Historial retorno</th>
    </tr>
    </thead>
    <tbody>
    @forelse($prestamosMaterial as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->cliente?->nombre }}</td>
            <td>{{ $p->inventario?->nombre }}</td>
            <td>{{ $p->estado }}</td>
            <td class="right">{{ $p->cantidad }}</td>
            <td class="right">{{ $p->retornado_cantidad }}</td>
            <td class="right">{{ $p->cantidad_actual }}</td>
            <td>
                @forelse($p->retornos as $r)
                    {{ $r->fecha ? \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') : '' }}:
                    cant. {{ $r->cantidad }}, monto {{ number_format((float) $r->efectivo, 2) }}
                    @if($r->observacion) - {{ $r->observacion }} @endif
                    <br>
                @empty
                    <span class="small">Sin retornos</span>
                @endforelse
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="center">Sin prestamos de material</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }} por {{ $usuario }} - Sistema Chicheria
</div>
</body>
</html>
