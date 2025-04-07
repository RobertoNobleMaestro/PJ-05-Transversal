<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Método para ver la vista del login
    public function login(){
        return view('auth.login');
    }

    public function loginProcess(Request $request){
        

    }
}
