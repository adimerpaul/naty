<?php

namespace App\Http\Controllers;

use App\Models\Loginventario;
use Illuminate\Http\Request;

class LoginventarioController extends Controller
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

    public function listlog(Request $request){
        return Loginventario::where('inventario_id',$request->id)->orderBy('id','desc')->get();
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loginventario  $Loginventario
     * @return \Illuminate\Http\Response
     */
    public function show(Loginventario $Loginventario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loginventario  $Loginventario
     * @return \Illuminate\Http\Response
     */
    public function edit(Loginventario $Loginventario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loginventario  $Loginventario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loginventario $Loginventario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loginventario  $Loginventario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loginventario $Loginventario)
    {
        //
    }
}
