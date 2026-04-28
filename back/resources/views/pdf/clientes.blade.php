<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; padding: 0; }

        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #1565c0;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-logo { display: table-cell; width: 80px; vertical-align: middle; }
        .header-logo img { width: 64px; height: 64px; object-fit: contain; }
        .header-info { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .header-info .empresa { font-size: 16px; font-weight: bold; color: #1565c0; letter-spacing: 0.5px; }
        .header-info .subtitulo { font-size: 12px; color: #444; margin-top: 2px; }
        .header-meta { display: table-cell; vertical-align: middle; text-align: right; font-size: 10px; color: #555; }
        .header-meta div { margin-bottom: 3px; }
        .header-meta .badge {
            display: inline-block;
            background: #1565c0;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            margin-top: 4px;
        }

        .resumen {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .resumen-item {
            display: table-cell;
            width: 33%;
            background: #e3f2fd;
            border-left: 4px solid #1565c0;
            padding: 6px 10px;
            margin-right: 6px;
            font-size: 11px;
        }
        .resumen-item .val { font-size: 16px; font-weight: bold; color: #1565c0; }
        .resumen-item .lbl { color: #555; }
        .resumen-gap { display: table-cell; width: 8px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1565c0; color: #fff; }
        thead th { padding: 8px 10px; font-size: 10px; text-align: left; letter-spacing: 0.3px; }
        tbody tr:nth-child(even) { background: #f5f9ff; }
        tbody tr:nth-child(odd) { background: #fff; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e8edf5; font-size: 10px; vertical-align: top; }
        tbody tr:last-child td { border-bottom: none; }
        .badge-activo { background: #c8f7c5; color: #1a7a1a; padding: 1px 6px; border-radius: 8px; font-size: 9px; }
        .badge-inactivo { background: #ffe0e0; color: #b00; padding: 1px 6px; border-radius: 8px; font-size: 9px; }

        .footer { margin-top: 16px; border-top: 1px solid #ddd; padding-top: 6px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-logo">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo">
        @endif
    </div>
    <div class="header-info">
        <div class="empresa">Chichería</div>
        <div class="subtitulo">Reporte de Clientes — {{ strtoupper($tipo) }}</div>
    </div>
    <div class="header-meta">
        <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        <div><strong>Generado por:</strong> {{ $usuario }}</div>
        <div><strong>Total registros:</strong> {{ $clientes->count() }}</div>
        <div><span class="badge">{{ strtoupper($tipo) }}</span></div>
    </div>
</div>

<div class="resumen">
    <div class="resumen-item">
        <div class="val">{{ $clientes->count() }}</div>
        <div class="lbl">Total clientes</div>
    </div>
    <div class="resumen-gap"></div>
    <div class="resumen-item">
        <div class="val">{{ $clientes->where('estado', true)->count() }}</div>
        <div class="lbl">Activos</div>
    </div>
    <div class="resumen-gap"></div>
    <div class="resumen-item">
        <div class="val">{{ $clientes->where('estado', false)->count() }}</div>
        <div class="lbl">Inactivos</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>CI</th>
        <th>Telefono</th>
        <th>Direccion</th>
        <th>Razon social</th>
        <th>NIT</th>
        <th>F. Nac.</th>
        <th>Observacion</th>
        <th>Estado</th>
    </tr>
    </thead>
    <tbody>
    @forelse($clientes as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td><strong>{{ $c->nombre }}</strong>@if($c->titular && $c->titular !== $c->nombre)<br><small style="color:#777">{{ $c->titular }}</small>@endif</td>
            <td>{{ ucfirst($c->tipo_cliente) }}</td>
            <td>{{ $c->ci }}</td>
            <td>{{ $c->telefono }}</td>
            <td>{{ $c->direccion }}</td>
            <td>{{ $c->razon_social }}</td>
            <td>{{ $c->nit }}</td>
            <td>{{ $c->fechanac ? \Carbon\Carbon::parse($c->fechanac)->format('d/m/Y') : '' }}</td>
            <td>{{ $c->observacion }}</td>
            <td>
                @if($c->estado)
                    <span class="badge-activo">Activo</span>
                @else
                    <span class="badge-inactivo">Inactivo</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="11" style="text-align:center; padding: 20px; color: #999;">Sin registros</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }} por {{ $usuario }} &mdash; Sistema Chichería
</div>
</body>
</html>
