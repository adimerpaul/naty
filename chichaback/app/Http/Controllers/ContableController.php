<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContableController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function repventa(Request $request){
       return  DB::SELECT("SELECT tipo, sum(acuenta) total from ventas where fecha>='$request->fecha1' AND fecha<='$request->fecha2' GROUP By tipo");

    }

    public function repventpago(Request $request){
        $res=DB::SELECT("SELECT sum(monto) total from pagos where fecha>='$request->fecha1' AND fecha<='$request->fecha2'");
        return $res;
    }

    public function repingprestamo(Request $request){
        return DB::SELECT("SELECT estado, SUM(efectivo) as total 
FROM prestamos 
WHERE fecha >= '$request->fecha1' 
AND fecha <= '$request->fecha2' 
AND estado = 'VENTA' 
GROUP BY estado

UNION

SELECT estado, SUM(efectivo) as total 
FROM prestamos 
WHERE fechaAnulacion >= '$request->fecha1' 
AND fechaAnulacion <= '$request->fecha2' 
AND estado = 'ANULADO' 
GROUP BY estado;");
    }

    public function repgastos(Request $request){
        return DB::SELECT("SELECT o.nombre as glosa,sum(precio) total FROM gastos g inner join glosas o on g.glosa_id=o.id WHERE glosa<>'CAJA CHICA' and fecha>='$request->fecha1' AND fecha<='$request->fecha2' group by o.nombre;");
    }

    public function repcompra(Request $request){
        return DB::SELECT("SELECT SUM(monto) as total from logcompras where fecha >= '$request->fecha1' and  fecha <= '$request->fecha2'");
    }

    public function repcaja(Request $request){
        return DB::SELECT("SELECT g.nombre as glosa,sum(monto) total FROM logcajas l inner join glosas g on l.glosa_id=g.id WHERE l.tipo='GASTO' and fecha>='$request->fecha1' AND fecha<='$request->fecha2' group by g.nombre");
    }

    public function totalCajaIng(Request $request){
        $res1= DB::SELECT("SELECT sum(monto) saldo FROM logcajas  WHERE tipo='AGREGA' and fecha>='$request->fecha1' AND fecha<='$request->fecha2' ") [0];
        $res2= DB::SELECT("SELECT sum(monto) total FROM logcajas  WHERE tipo='RETIRA' and fecha>='$request->fecha1' AND fecha<='$request->fecha2' ")[0];
        return $res1->saldo - $res2->total;
    }

    public function repcaja2(Request $request){
        return DB::SELECT("SELECT SUM(monto) as total from logcajas 
        where fecha >= '$request->fecha1' and  fecha <= '$request->fecha2'
        and tipo='RETIRA' ");
    }
}
