<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuentas por Cobrar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .fecha-rango {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>
<h2>Reporte de Cuentas por Cobrar</h2>
<div class="fecha-rango">
    <strong>Desde:</strong> {{ $fechaInicio }} &nbsp;&nbsp;
    <strong>Hasta:</strong> {{ $fechaFin }}
</div>

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Teléfono</th>
        <th>Total</th>
        <th>A Cuenta</th>
        <th>Saldo</th>
        <th>Usuario</th>
    </tr>
    </thead>
    <tbody>
    @foreach($ventas as $i => $venta)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $venta->fecha }}</td>
            <td>{{ $venta->cliente->titular ?? 'Sin nombre' }}</td>
            <td>{{ $venta->cliente->telefono ?? '-' }}</td>
            <td>{{ number_format($venta->total, 2) }}</td>
            <td>{{ number_format($venta->acuenta, 2) }}</td>
            <td>{{ number_format($venta->total - $venta->acuenta, 2) }}</td>
            <td>{{ $venta->user->name ?? '---' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
