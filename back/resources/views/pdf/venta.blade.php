<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta #{{ $venta->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h2 { margin: 0 0 10px 0; }
        .meta { margin-bottom: 12px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d5d5d5; padding: 6px; vertical-align: top; }
        th { background: #f1f1f1; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
<h2>Venta #{{ $venta->id }}</h2>
<div class="meta">
    <strong>Fecha:</strong> {{ optional($venta->created_at)->format('d/m/Y H:i') }}<br>
    <strong>Cliente:</strong> {{ $venta->cliente_nombre ?: '-' }}<br>
    <strong>Telefono:</strong> {{ $venta->cliente_telefono ?: '-' }}<br>
    <strong>Direccion:</strong> {{ $venta->cliente_direccion ?: '-' }}<br>
    <strong>Usuario:</strong> {{ $venta->user?->name ?: ($venta->user?->username ?: '-') }}<br>
    <strong>Tipo:</strong> {{ ucfirst($venta->tipo_movimiento ?: 'ingreso') }}<br>
    <strong>Pago:</strong> {{ ucfirst($venta->tipo_pago) }}<br>
    <strong>Observacion:</strong> {{ $venta->observacion ?: '-' }}
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Producto / Concepto</th>
        <th class="right">Precio</th>
        <th class="right">Cantidad</th>
        <th class="right">Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @forelse($venta->detalles as $d)
        <tr>
            <td>{{ $d->id }}</td>
            <td>{{ $d->producto_nombre }}</td>
            <td class="right">{{ number_format((float)$d->precio, 2, '.', ',') }}</td>
            <td class="right">{{ number_format((float)$d->cantidad, 2, '.', ',') }}</td>
            <td class="right">{{ number_format((float)$d->subtotal, 2, '.', ',') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5">Sin detalle (movimiento ingreso/egreso)</td>
        </tr>
    @endforelse
    </tbody>
</table>

<table>
    <tbody>
    <tr>
        <th style="width: 70%">Total</th>
        <th class="right">{{ number_format((float)$venta->total, 2, '.', ',') }} Bs</th>
    </tr>
    </tbody>
</table>
</body>
</html>
