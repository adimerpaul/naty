<?php

namespace App\Http\Controllers;

use App\Models\Loggeneral;
use App\Models\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoggeneralController extends Controller
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

    public function listgeneral(Request $request){
        //return $request;
        return Loggeneral::with('glosa')->with('user')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('fecha','desc')->get();

    }

    public function totalgeneral(){
        return DB::SELECT('SELECT * from generals where id=1')[0];
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
        $log=new Loggeneral ;
        $log->numero=0;
        $log->detalle='';
        $log->monto=$request->monto;
        $log->motivo=$request->motivo;
        $log->tipo=$request->tipo;
        $log->fecha=date('Y-m-d');
        $log->hora=date('H:i:s');
        $log->glosa_id=null;
        $log->user_id=$request->user()->id;
        $log->save();
        $caja=General::find(1);

        if($request->tipo=='RETIRAR')
            $caja->monto=floatval($caja->monto) - floatval($request->monto);

        else
            $caja->monto=floatval($caja->monto) + floatval($request->monto);

            $caja->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loggeneral  $loggeneral
     * @return \Illuminate\Http\Response
     */
    public function show(Loggeneral $loggeneral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loggeneral  $loggeneral
     * @return \Illuminate\Http\Response
     */
    public function edit(Loggeneral $loggeneral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loggeneral  $loggeneral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loggeneral $loggeneral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loggeneral  $loggeneral
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $caja=General::find(1);
        $loggeneral=Loggeneral::find($id);

        if($loggeneral->tipo=='EGRESO' || $loggeneral->tipo=='RETIRAR' || $loggeneral->tipo=='GASTO'){
            $caja->monto=floatval($caja->monto) + floatval($loggeneral->monto);
            $caja->save();

        }
        if($loggeneral->tipo=='AGREGAR' || $loggeneral->tipo=='INGRESO'){
            $caja->monto=floatval($caja->monto) - floatval($loggeneral->monto);
            $caja->save();

        }
        $loggeneral->delete();
    }
}
