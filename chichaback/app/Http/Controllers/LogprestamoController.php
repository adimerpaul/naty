<?php

namespace App\Http\Controllers;

use App\Models\Logprestamo;
use App\Models\Prestamo;
use App\Models\Inventario;
use App\Models\Cliente;
use Illuminate\Http\Request;

class LogprestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $prestamo=Prestamo::find($request->id);
        if(!$prestamo){
            return response()->json(['error' => 'Prestamo no encontrado.'], 404);
        }
        if($prestamo->prestado - $request->cantidad < 0){
            return response()->json(['error' => 'No hay suficiente cantidad prestada para devolver.'], 400);
        }   
        $logpres= new Logprestamo;
        $logpres->fecha=$request->fecha;
        $logpres->cantidad=$request->cantidad;
        $logpres->motivo=$request->motivo;
        $logpres->prestamo_id=$request->id;
        $logpres->user_id=$request->user()->id;
        $logpres->save();
        $prestamo->prestado = $prestamo->prestado - $request->cantidad;
        if($prestamo->prestado <= 0)
        $prestamo->estado='DEVUELTO';
        $prestamo->save();
        $inv=Inventario::find($request->inventario_id);
        $inv->cantidad= $inv->cantidad + $request->cantidad;
        $inv->save();
        $cliente=Cliente::find($prestamo->cliente_id);
        $cadena="
<table>

<tr><td colspan ='2' style='font-size:10px; text-align:center;'><b>COMPROBANTE DE DEVOLUCION <br> DE GARANTIA</b></td></tr>
<tr><td><b>Fecha:</b></td><td>".date('d/m/Y',strtotime($logpres->fecha))."</td></tr>
<tr><td><b>Cliente:</b></td><td>$cliente->local  $cliente->titular</td></tr>
<tr><td><b>Item:</b></td><td> $inv->nombre</td></tr>
<tr><td><b>Cantidad:</b></td><td>$logpres->cantidad</td></tr>
<tr><td><b>Efectivo:</b></td><td>$prestamo->efectivo</td></tr>
<tr><td><b>Fisico:</b></td><td>$prestamo->fisico</td></tr>
<tr><td><b>Usuario:</b></td><td>".$request->user()->name."</td></tr>
</table>
<br>
<br>
<div style='text-align:center'>FIRMA</div>

";

//        <div> Fecha: $logpres->fecha</div>
//        <div>Cliente: $cliente->local  $cliente->titular</div>
//        <div>Item: $inv->nombre</div>
//        <div>Cantidad: $logpres->cantidad</div>
//        <div>Efectivo: $prestamo->efectivo</div>
//        <div>Fisico: $prestamo->fisico</div>
//        <div>Usuario: ".$request->user()->name."</div><br><br>
//        <div style='text-align:center'>FIRMA</div>
        return $cadena;

    }

    public function impdevolucion($id){
        $devol=Logprestamo::where('id',$id)->with('prestamo')->with('user')->first();
        $cliente=Cliente::find($devol->prestamo->cliente['id']);
        $inv=Inventario::find($devol->prestamo->inventario['id']);

        $cadena="
        <table>

        <tr><td colspan ='2' style='font-size:10px; text-align:center;'><b>COMPROBANTE DE DEVOLUCION <br> DE GARANTIA</b></td></tr>
        <tr><td><b>Fecha:</b></td><td>".date('d/m/Y',strtotime($devol->fecha))."</td></tr>
        <tr><td><b>Cliente:</b></td><td>$cliente->local  $cliente->titular</td></tr>
        <tr><td><b>Item:</b></td><td> $inv->nombre</td></tr>
        <tr><td><b>Cantidad:</b></td><td>$devol->cantidad</td></tr>
        <tr><td><b>Efectivo:</b></td><td>".$devol->prestamo->efectivo."</td></tr>
        <tr><td><b>Fisico:</b></td><td>".$devol->prestamo->fisico."</td></tr>
        <tr><td><b>Usuario:</b></td><td>".$devol->user['name']."</td></tr>
        </table>
        <br>
        <br>
        <div style='text-align:center'>FIRMA</div>

        ";
        return $cadena;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Logprestamo  $logprestamo
     * @return \Illuminate\Http\Response
     */
    public function show(Logprestamo $logprestamo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Logprestamo  $logprestamo
     * @return \Illuminate\Http\Response
     */
    public function edit(Logprestamo $logprestamo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Logprestamo  $logprestamo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logprestamo $logprestamo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Logprestamo  $logprestamo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $logpres= Logprestamo::find($id);

        $prestamo=Prestamo::find($logpres->prestamo_id);
        $prestamo->prestado = $prestamo->prestado + $logpres->cantidad;
        $prestamo->estado='EN PRESTAMO';
        $prestamo->save();

        $inv=Inventario::find($prestamo->inventario_id);
        $inv->cantidad= $inv->cantidad - $logpres->cantidad;
        $inv->save();

        $logpres->delete();
    }
}
