<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h2 { margin: 0 0 8px 0; }
        .meta { margin-bottom: 12px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d0d0d0; padding: 6px; vertical-align: top; }
        th { background: #f1f1f1; text-align: left; }
    </style>
</head>
<body>
<h2>Clientes - {{ strtoupper($tipo) }}</h2>
<div class="meta">Generado: {{ now()->format('Y-m-d H:i') }} | Total: {{ $clientes->count() }}</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>CI</th>
        <th>Telefono</th>
        <th>Direccion</th>
        <th>Observacion</th>
        <th>Lat</th>
        <th>Lng</th>
        <th>Estado</th>
    </tr>
    </thead>
    <tbody>
    @forelse($clientes as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->nombre }}</td>
            <td>{{ ucfirst($c->tipo_cliente) }}</td>
            <td>{{ $c->ci }}</td>
            <td>{{ $c->telefono }}</td>
            <td>{{ $c->direccion }}</td>
            <td>{{ $c->observacion }}</td>
            <td>{{ $c->lat }}</td>
            <td>{{ $c->lng }}</td>
            <td>{{ $c->estado ? 'Activo' : 'Inactivo' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10">Sin registros</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
