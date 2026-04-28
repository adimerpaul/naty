<?php

use App\Services\Impuestos\SiatService;
use Illuminate\Support\Carbon;

it('calcula la vigencia del CUFD hasta el fin del dia de creacion', function () {
    $service = app(SiatService::class);
    $method = new ReflectionMethod($service, 'fechaVigenciaCufd');
    $fechaCreacion = Carbon::parse('2026-04-28 14:35:10');

    $vigencia = $method->invoke($service, $fechaCreacion);

    expect($vigencia->format('Y-m-d H:i:s'))->toBe('2026-04-28 23:59:59');
    expect($fechaCreacion->format('Y-m-d H:i:s'))->toBe('2026-04-28 14:35:10');
});
