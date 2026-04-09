<?php

namespace App\Http\Controllers;

use App\Models\Logcaja;
use App\Models\Loggeneral;
use App\Models\Caja;
use App\Models\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogcajaController extends Controller
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
    public function listcaja(Request $request){
        //return $request;
        //if($request->user()->id==1)
        //{
            if($request->user_id==0)
            return Logcaja::with('glosa')->with('user')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('fecha','desc')->orderBy('id','desc')->get();
            else
            return Logcaja::with('glosa')->with('user')->where('user_id',$request->user_id)->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('fecha','desc')->orderBy('id','desc')->get();
        //}
        //else
        //return Logcaja::with('glosa')->with('user')->where('user_id',$request->user()->id)->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->get();

    }

    public function totalcaja(){
        return DB::SELECT('SELECT * from cajas where id=1')[0];
    }

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
        $log=new Logcaja ;
        $log->monto=$request->monto;
        $log->motivo=$request->motivo;
        $log->tipo=$request->tipo;
        $log->fecha=date('Y-m-d');
        $log->hora=date('H:i:s');
        $log->glosa_id=null;
        $log->user_id=$request->user()->id;
        $log->save();
        $caja=Caja::find(1);

        $general=General::find(1);
        $loggeneral=new Loggeneral ;
        $loggeneral->numero=0;
        $loggeneral->detalle='';
        $loggeneral->monto=$request->monto;
        $loggeneral->motivo=$request->motivo .' TRANSACCION CCHICA';
        if($request->tipo=='RETIRA'){
            $loggeneral->tipo='AGREGAR';
        }
        else{
            $loggeneral->tipo='RETIRAR';
        }
        $loggeneral->fecha=date('Y-m-d');
        $loggeneral->hora=date('H:i:s');
        $loggeneral->glosa_id=null;
        $loggeneral->user_id=$request->user()->id;
        $loggeneral->save();

        if($request->tipo=='RETIRA'){
            $caja->monto=floatval($caja->monto) - floatval($request->monto);
            $caja->save();
            $general->monto=floatval($general->monto) + floatval($request->monto);
            $general->save();
        }
        else{
            $caja->monto=floatval($caja->monto) + floatval($request->monto);
            $caja->save();
            $general->monto=floatval($general->monto) - floatval($request->monto);
            $general->save();

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Logcaja  $logcaja
     * @return \Illuminate\Http\Response
     */
    public function show(Logcaja $logcaja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Logcaja  $logcaja
     * @return \Illuminate\Http\Response
     */
    public function edit(Logcaja $logcaja)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Logcaja  $logcaja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logcaja $logcaja)
    {
        //
        $log=Logcaja::find($request->id) ;


        $caja=Caja::find(1);
        $caja->monto = floatval($caja->monto) + floatval($log->monto) - floatval($request->monto);

        $log->monto=$request->monto;
        $log->motivo=$request->motivo;
        $log->glosa_id=$request->glosa_id;
        $caja->save();
        $log->save();

        return $caja->monto;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Logcaja  $logcaja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logcaja $logcaja)
    {
        //

        if($logcaja->tipo=='GASTO' || $logcaja->tipo=='RETIRA'){
            $caja=Caja::find(1);
            $caja->monto=floatval($caja->monto) + floatval($logcaja->monto);
            $caja->save();

        }
        if($logcaja->tipo=='AGREGA'){
            $caja=Caja::find(1);
            $caja->monto=floatval($caja->monto) - floatval($logcaja->monto);
            $caja->save();

        }
        $logcaja->delete();

    }
    public function repcaja(Request $request){
        return DB::SELECT("SELECT SUM(total) as total from planillas where fechapago >= '$request->fecha1' and  fechapago <= '$request->fecha2'");
    }

    public function informeCaja(Request $request){
        $inicio=$request->input('inicio');
        $fin=$request->input('fin');
        $tipo=$request->input('tipo');

        $caja=Logcaja::whereBetween('fecha',[$inicio,$fin])
        ->where('tipo',$tipo)
        ->get();
        // retorna sumatoria del monto
        $suma=0;
        foreach($caja as $c){
            $suma=$suma + $c->monto;
        }
        return $suma;
    }

}
