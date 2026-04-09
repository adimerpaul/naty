<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Cliente::with('prestamos')->get();
    }
    public function agregarcliente(Request $request){
        $cliente= new Cliente();
        $cliente->ci=$request->ci;
        $cliente->titular= strtoupper( $request->titular);
        $cliente->telefono=$request->telefono;
        $cliente->direccion= strtoupper($request->direccion);
        $cliente->tipocliente=$request->tipocliente;
        $cliente->save();
//        Cliente::create($request->all());
    }

    public function listacliente(){
        return Cliente::where('estado','ACTIVO')->orderBy('titular')->get();
    }
    public function listacliente2($type){
        if ($type=='detalle') {
            return Cliente::whereTipocliente('2')->where('estado', 'ACTIVO')->orderBy('titular')->get();
        }
        if ($type=='local')
            {
            return Cliente::whereTipocliente('1')->where('estado', 'ACTIVO')->orderBy('titular')->get();
        }

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
        $cliente= new Cliente;
        $cliente->local=strtoupper($request->local);
        $cliente->ci=strtoupper($request->ci);
        $cliente->titular=strtoupper($request->titular);
        $cliente->tipo=$request->tipo;
        $cliente->telefono=strtoupper($request->telefono);
        $cliente->fechanac=$request->fechanac;
        $cliente->direccion=strtoupper($request->direccion);
        $cliente->legalidad=$request->legalidad;
        $cliente->categoria=$request->categoria;
        $cliente->razon=strtoupper($request->razon);
        $cliente->nit=strtoupper($request->nit);
        $cliente->observacion=strtoupper($request->observacion);
        $cliente->tipocliente=$request->tipocliente;
        $cliente->save();
        return $cliente;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return Cliente::find($id)->get() ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        //
        $cliente=Cliente::find($request->id);
        $cliente->ci=strtoupper($request->ci);
        $cliente->local=strtoupper($request->local);
        $cliente->titular=strtoupper($request->titular);
        $cliente->tipo=$request->tipo;
        $cliente->telefono=strtoupper($request->telefono);
        $cliente->fechanac=$request->fechanac;
        $cliente->direccion=strtoupper($request->direccion);
        $cliente->legalidad=$request->legalidad;
        $cliente->categoria=$request->categoria;
        $cliente->razon=strtoupper($request->razon);
        $cliente->nit=strtoupper($request->nit);
        $cliente->observacion=strtoupper($request->observacion);
        $cliente->save();
        return $cliente;
    }

    public function activar(Request $request)
    {
        //
        $cliente=Cliente::find($request->id);
        if($cliente->estado=='ACTIVO')
            $cliente->estado='INACTIVO';
        else {
            $cliente->estado='ACTIVO';
        }
        $cliente->save();
        return $cliente;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $cliente=Cliente::find($id);
        $cliente->delete();
        return $cliente;
    }
    public function cumple3()
    {
        $hoy = Carbon::today();
        $inicio = $hoy->copy()->subDays(7);
        $fin = $hoy->copy()->addDays(7);

        $clientes = Cliente::all()->map(function ($cliente) use ($hoy, $inicio, $fin) {
            if (!$cliente->fechanac) {
                return null;
            }

            $nacimiento = Carbon::parse($cliente->fechanac);
            $proximoCumple = $nacimiento->copy()->year($hoy->year);

            // Si ya pasó este año, considerar el del siguiente año
            if ($proximoCumple->lessThan($inicio)) {
                $proximoCumple->addYear();
            }

            if (!$proximoCumple->between($inicio, $fin)) {
                return null;
            }

            $diasFaltantes = $hoy->diffInDays($proximoCumple, false); // puede ser negativo

            $ventas = $cliente->ventas()
                ->orderBy('fecha', 'desc')
                ->take(7)
                ->get(['fecha', 'total']);

            return [
                'cliente' => $cliente->titular,
                'local' => $cliente->local,
                'fechanac' => $cliente->fechanac,
                'dias_para_cumple' => $diasFaltantes,
                'ventas' => $ventas
            ];
        })->filter()->sortBy('dias_para_cumple')->values();

        return response()->json($clientes);
    }



    public function ordercumple(){

         $cliente=DB::select('(select *,MONTH(fechanac) as mes,DAY(fechanac) as dia from clientes
         where MONTH(fechanac)>=MONTH(CURDATE()) order by  mes asc, dia asc)
         ');
         return $cliente;
    }
    public function ordercumple2(){
        $cliente2=DB::select('
        (select *,MONTH(fechanac) as mes,DAY(fechanac) as dia from clientes
         where MONTH(fechanac)<MONTH(CURDATE()) order by  mes asc, dia asc)
         union (select *,"" as mes,"" as dia from clientes where fechanac is null)');
         return $cliente2;
    }

    public function aniver(){
        $fec= date("Y-m-d",strtotime(date('Y-m-d')));
        $cliente=DB::SELECT("SELECT *,MONTH(fechanac) as mes,DAY(fechanac) as dia from clientes
         where MONTH(fechanac)=MONTH(CURDATE()) and DAY(fechanac)=DAY(CURDATE()) and
          estado='ACTIVO' order by dia asc");
         // where MONTH(fechanac)>=MONTH(CURDATE()) and MONTH(fechanac)<=MONTH('$fec') and DAY(fechanac)>=DAY(CURDATE()) and DAY(fechanac)<=DAY('$fec') and
         return $cliente;
    }

    public function listado(){
        //return Cliente::where('tipocliente',$tipocliente)->get();
    }


    public function buscarci($request){
        return Cliente::where('tipocliente',$request->tipocliente)->where('ci',$request->ci)->get();
    }

    public function buscarnombre($request){
        return Cliente::where('tipocliente',$request->tipocliente)->where('titular',$request->titular)->get();
    }

    public function buscarlocal($request){
        return Cliente::where('tipocliente',$request->tipocliente)->where('local',$request->local)->get();
    }
}
