<?php

namespace App\Http\Controllers;

use App\Models\Logplanilla;
use Illuminate\Http\Request;

class LogplanillaController extends Controller
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
//        return $request;
        Logplanilla::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Logplanilla  $logplanilla
     * @return \Illuminate\Http\Response
     */
    public function show($planilla_id)
    {
        return Logplanilla::where('planilla_id',$planilla_id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Logplanilla  $logplanilla
     * @return \Illuminate\Http\Response
     */
    public function edit(Logplanilla $logplanilla)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Logplanilla  $logplanilla
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logplanilla $logplanilla)
    {
        $logplanilla->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Logplanilla  $logplanilla
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logplanilla $logplanilla)
    {
        $logplanilla->delete();
    }
}
