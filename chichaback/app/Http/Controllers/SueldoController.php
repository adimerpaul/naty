<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Sueldo;
use App\Models\Gasto;
use App\Models\Glosa;
use App\Models\Caja;
use App\Models\Logcaja;
use App\Models\General;
use App\Models\Loggeneral;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Else_;

class SueldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Sueldo::all();
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
        //return $request;
//        return Sueldo::create($request->all());
        $sueldo=new Sueldo();
        $sueldo->fecha=$request->fecha;
        $sueldo->hora=date('H:i:s');
        $sueldo->monto=$request->monto;
        $sueldo->tipo=$request->tipo;
        $sueldo->empleado_id=$request->empleado_id;
        $sueldo->observacion=$request->observacion;
        $sueldo->user_id=$request->user()->id;
        if($request->checkbox=='CAJA')
            $sueldo->caja='CHICA';
        else
            $sueldo->caja='GENERAL';
        $sueldo->save();

        if($request->checkbox=='CAJA'){
        if($request->tipo=='ADELANTO' || $request->tipo=='EXTRA' ){
            $caja=Caja::find(1);
            $caja->monto= floatval($caja->monto) - floatval($request->monto);
            $caja->save();

            $glosa=Glosa::where('nombre',$request->tipo)->get()[0];
            $log=new Logcaja ;
            $log->monto=$request->monto;
            $log->motivo=$request->observacion.' '.$request->empleado_nombre;;
            $log->tipo='GASTO';
            $log->glosa_id=$glosa->id;
            $log->fecha=date('Y-m-d');
            $log->hora=date('H:i:s');
            $log->user_id=$request->user()->id;
            $log->sueldo_id=$sueldo->id;
            $log->save();
        }}
        else{
        if($request->tipo=='ADELANTO' || $request->tipo=='EXTRA' ){
            $glosa=Glosa::where('nombre',$request->tipo)->get()[0];

            $gasto=new Gasto();
            $gasto->precio=$request->monto;
            $gasto->observacion=$request->observacion.' '.$request->empleado_nombre;
            $gasto->glosa=$request->tipo;
            $gasto->glosa_id=$glosa->id;
            $gasto->fecha=date('Y-m-d');
            $gasto->hora=date('H:i:s');
            $gasto->user_id=$request->user()->id;
            $gasto->save();

            $general=General::find(1);
            $general->monto=$general->monto - $request->monto;
            $general->save();

            $loggeneral= new Loggeneral;
            $loggeneral->numero=$gasto->id;
            $loggeneral->monto= $request->monto;
            $loggeneral->detalle='EGRESO';
            $loggeneral->motivo=$request->observacion;
            $loggeneral->tipo='EGRESO';
            $loggeneral->fecha=$gasto->fecha;
            $loggeneral->hora=date("H:i:s");
            $loggeneral->glosa_id=$glosa->id;
            $loggeneral->user_id=$request->user()->id;
            $loggeneral->sueldo_id=$sueldo->id;
            $loggeneral->save();

        }


        }
        $info=Sueldo::with('empleado')->find($sueldo->id);
        $cadena="<center>$info->tipo</center>
        <div>Empleado: ".$info->empleado['nombre']."</div>
        <div>Fecha: ".date('d/m/Y',strtotime($info->fecha))."</div>
        <div>Monto: $info->monto Bs.</div>
        <div>Usuario: ".$request->user()->name."</div>
        <div>Obs: $info->observacion </div><br><br>
        <center>FIRMA</center>
        ";
        return $cadena;
//        return $sueldo;
        //return Sueldo::where('empleado_id',$request->empleado_id)->get();
    }

    public function cancelacion(Request $request){
            //return $request;

            $sueldo=new Sueldo();
            $sueldo->fecha=$request->fecha;
            $sueldo->hora=date('H:i:s');
            $sueldo->monto=$request->monto;
            $sueldo->tipo='CANCELAR';
            $sueldo->empleado_id=$request->empleado_id;
            $sueldo->observacion='Cancelado';
            $sueldo->user_id=$request->user()->id;
            $sueldo->save();

              if($request->tipo=='CANCELAR'){

                $gasto=new Gasto();
                $gasto->precio=$request->monto;
                $gasto->observacion='Cancelado'.' '.$request->empleado_nombre;
                $gasto->glosa='CANCELAR';
                $gasto->fecha=$request->fecha;
                $gasto->hora=date('H:i:s');
                $gasto->user_id=$request->user()->id;
                $gasto->save();
            }
    //        return $sueldo;
            return Empleado::with('sueldos')
                ->where('id',$request->empleado_id)
                ->firstOrFail();
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sueldo  $sueldo
     * @return \Illuminate\Http\Response
     */
    public function show(Sueldo $sueldo)
    {
        //
    }

    public function impade($id,Request $request){
        $info=Sueldo::with('empleado')->find($id);
        $cadena="<center>$info->tipo</center>
        <div><b>Empleado:</b> ".$info->empleado['nombre']."</div>
        <div><b>Fecha:</b> ".date('d/m/Y',strtotime($info->fecha))."</div>
        <div><b>Monto:</b> $info->monto Bs.</div>
        <div><b>Usuario:</b> ".$request->user()->name."</div><br><br>
        <div><b>Obs:</b> $info->observacion </div>
        <center>FIRMA</center>
        ";
        return $cadena;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sueldo  $sueldo
     * @return \Illuminate\Http\Response
     */
    public function edit(Sueldo $sueldo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sueldo  $sueldo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sueldo $sueldo)
    {
        return $sueldo->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sueldo  $sueldo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sueldo $sueldo,Request $request)
    {
        $sueldo=Sueldo::find($sueldo->id);
        $empleado=Empleado::find($sueldo->empleado_id);
        if($sueldo->caja=='GENERAL'){
            if ($sueldo->tipo=='ADELANTO' || $sueldo->tipo=='EXTRA'){
                $loggeneral=Loggeneral::where('tipo','EGRESO')->where('sueldo_id',$sueldo->id)->first();
                if($loggeneral ==null){
                    return response()->json(['error'=>'No se puede anular este sueldo, ya que no se ha realizado un log general.'], 400);
                }
                $gasto=Gasto::where('observacion',$sueldo->observacion.' '.$empleado->nombre)
                    ->where('glosa',$sueldo->tipo)
                    ->first();
                $gasto->delete();
                $loggeneral->delete();

                $general=General::find(1);
                $general->monto=$general->monto + $sueldo->monto;
                $general->save();

            }
        }
        else {

            if ($sueldo->tipo=='ADELANTO' || $sueldo->tipo=='EXTRA'){
                //verificar primero si esta en Locaja si no esta error
                $log=Logcaja::where('tipo','GASTO')->where('sueldo_id',$sueldo->id)->first();//
                if($log ==null){
                    return response()->json(['error'=>'No se puede anular este sueldo, ya que se ha realizado un log de caja.'], 400);
            $caja=Caja::find(1);
            $caja->monto= floatval($caja->monto) + floatval($sueldo->monto);
            $caja->save();

            $log->delete();
        }
        }
//        $sueldo->delete();
//        return 1;
    }
        $sueldo->update(['monto'=>0,'tipo'=>'ANULADO','observacion'=>$sueldo->observacion.', '.$request->motivo]);
    // SI ES DESCUENTO SOLO ANULAR

}
}
