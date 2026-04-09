<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Planilla;
use App\Models\Caja;
use App\Models\Logcaja;
use App\Models\General;
use App\Models\Loggeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanillaController extends Controller
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
        //return $request;
        $planilla= new Planilla;
        $planilla->fechainicio=$request->fechainicio;
        $planilla->fechafin=$request->fechafin;
        $planilla->fechapago=$request->fechapago;
        $planilla->monto=$request->monto;
        $planilla->adelanto=$request->adelanto;
        $planilla->descuento=$request->descuento;
        $planilla->bono=$request->bono;
        $planilla->restante=$request->restante;
        $planilla->total=$request->total;
        $planilla->observacion=$request->observacion;
        $planilla->empleado_id=$request->empleado_id;
        $planilla->save();


        $general=General::find(1);
        $general->monto=floatval($general->monto) - floatval($planilla->total);
        $general->save();

        $loggeneral= new Loggeneral;
        $loggeneral->numero=$planilla->id;
        $loggeneral->monto= $planilla->total;
        $loggeneral->detalle='SALARIO';
        $loggeneral->motivo='PAGO SALARIO '.$planilla->id;
        $loggeneral->tipo='EGRESO';
        $loggeneral->fecha=$planilla->fechapago;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=null;
        $loggeneral->user_id=$request->user()->id;
        $loggeneral->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\Response
     */
    public function show($empleado_id)
    {
//        return $empleado_id;
        return Planilla::where('empleado_id',$empleado_id)->orderBy('id','desc')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\Response
     */
    public function edit(Planilla $planilla)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planilla $planilla)
    {
        $planilla->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$general=General::find(1);
        $general->monto=$general->monto + $planilla->total;
        $general->save();

        $loggeneral= Loggeneral::where('numero',$planilla->id)->where('tipo','EGRESO')->where('detalle','SALARIO')->get()[0];
        $loggeneral->delete();*/
        $planilla=Planilla::find($id);
        if(Planilla::where('id',$id)->count()==0)
            return response()->json(['error' => 'No Existe Planilla'], 500);
        if(Loggeneral::where('numero',$planilla->id)->whereDate('fecha',$planilla->fechapago)->where('detalle','SALARIO')->count()==0)
            return response()->json(['error' => 'No hay Registro Caja G'], 500);
        

        $general=General::find(1);
        $general->monto=floatval($general->monto) + floatval($planilla->total);
        $general->save();

        $loggeneral= Loggeneral::where('numero',$planilla->id)->whereDate('fecha',$planilla->fechapago)
        ->where('detalle','SALARIO')->first();
        $loggeneral->delete();

        $planilla->delete();
    }

    public function valplanilla(Request $request){
        return Planilla::whereDate('fechainicio',$request->fechainicio)->whereDate('fechafin',$request->fechafin)->where('empleado_id',$request->empleado_id)->get();
    }

    public function replanilla(Request $request){
        return DB::SELECT("SELECT SUM(total) as total from planillas where fechapago >= '$request->fecha1' and  fechapago <= '$request->fecha2'");
    }
}
