<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Pago</title>
    <style>
        body { font-family: "Times New Roman", serif; font-size: 14px; margin: 20px; }
        .head { text-align: center; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
        .title { font-size: 28px; font-weight: bold; }
        .sub { font-size: 18px; font-weight: bold; }
        .row { width: 100%; margin-bottom: 4px; }
        .row td { padding: 2px 4px; }
        .box { border: 1px solid #444; margin-top: 8px; }
        .box th { background: #efefef; }
        .tot { font-size: 24px; font-weight: bold; text-align: center; margin-top: 8px; }
        .firma { margin-top: 60px; width: 100%; }
        .firma td { width: 50%; text-align: center; }
        .linea { border-top: 1px solid #222; width: 220px; margin: 0 auto 6px auto; }
    </style>
</head>
<body>
<div class="head">
    <div class="title">CHICHERIA DOÑA NATY</div>
    <div class="sub">BOLETA DE PAGO</div>
    <div>Correspondiente {{ $periodo_inicio }} al {{ $periodo_fin }}</div>
    <div>Fecha de pago: {{ $pago->fecha_pago }}</div>
</div>

<table class="row">
    <tr>
        <td><b>CI:</b> {{ $pago->personal->ci ?? '' }}</td>
        <td><b>Tipo:</b> {{ $pago->personal->tipo ?? '' }}</td>
    </tr>
    <tr>
        <td><b>Nombre:</b> {{ $pago->personal->nombre ?? '' }}</td>
        <td><b>Caja:</b> {{ $pago->caja->nombre ?? '' }}</td>
    </tr>
</table>

<table class="box" width="100%" cellspacing="0" cellpadding="4">
    <thead>
    <tr>
        <th>INGRESOS</th>
        <th>DESCUENTOS</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            Salario: {{ number_format($pago->sueldo, 2) }} Bs<br>
            Extras: {{ number_format($pago->extras, 2) }} Bs
        </td>
        <td>
            Adelantos: {{ number_format($pago->adelantos, 2) }} Bs<br>
            Descuentos: {{ number_format($pago->descuentos, 2) }} Bs
        </td>
    </tr>
    <tr>
        <td><b>Total ganado:</b> {{ number_format($pago->sueldo + $pago->extras, 2) }} Bs</td>
        <td><b>Total descuento:</b> {{ number_format($pago->adelantos + $pago->descuentos, 2) }} Bs</td>
    </tr>
    </tbody>
</table>

<div class="tot">Liquido pagable: {{ number_format($pago->monto_pagado, 2) }} Bs</div>
<div><b>Obs:</b> {{ $pago->observacion ?? '' }}</div>

<table class="firma">
    <tr>
        <td>
            <div class="linea"></div>
            FIRMA EMPLEADO
        </td>
        <td>
            <div class="linea"></div>
            FIRMA RESPONSABLE
        </td>
    </tr>
</table>
</body>
</html>

