<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableroController extends Controller
{
    public function kumite()
    {
        return view('kumite.tablero');
    }

        public function kata()
    {
        return view('kata.tablero');
    }
}