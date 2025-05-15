<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChoferController extends Controller
{
    public function dashboard(){
        return view('chofers.dashboard');
    }
}
