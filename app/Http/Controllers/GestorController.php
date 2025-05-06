<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestorController extends Controller
{
    public function dashboard(){
        return view('gestor.index');
    }
}
