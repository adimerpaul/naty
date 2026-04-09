<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller{
    function cuentasCobrarLocal(Request $request){
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $estado = $request->input('estado');
        $ventas = Venta::with(['user', 'cliente', 'pagos'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('tipo', 'LOCAL')
            ->where(function ($query) {
                $query->whereNull('fechaentrega')
                    ->orWhere('fechaentrega', '');
            })
            ->whereRaw('total > acuenta');

        if ($estado !== 'TODOS') {
            $ventas->where('estado', $estado);
        }

        $ventas = $ventas->get();
        $pdf = Pdf::loadView('pdf.cuentascobrarlocal', [
            'ventas' => $ventas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
        return $pdf->stream();
    }
    function cuentasCobrarDetalle(Request $request){
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $estado = $request->input('estado');

        $ventas = Venta::with(['user', 'cliente', 'pagos'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('tipo', 'DETALLE')
            ->where(function ($query) {
                $query->whereNull('fechaentrega')
                    ->orWhere('fechaentrega', '');
            })
            ->whereRaw('total > acuenta');

        if ($estado !== 'TODOS') {
            $ventas->where('estado', $estado);
        }

        $ventas = $ventas->get();


        $pdf = Pdf::loadView('pdf.cuentascobrardetalle', [
            'ventas' => $ventas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
        return $pdf->stream();
    }
}
