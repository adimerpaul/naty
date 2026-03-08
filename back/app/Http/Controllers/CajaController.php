<?php

namespace App\Http\Controllers;

use App\Models\Caja;

class CajaController extends Controller
{
    public function index()
    {
        return Caja::where('estado', true)->orderBy('id')->get();
    }
}

