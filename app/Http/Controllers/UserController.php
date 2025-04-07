<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        return view('admin.add_user');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'dni' => 'required|string|max:15',
            'telefono' => 'required|string|max:15',
            'fecha_nacimiento' => 'required|date',
            'direccion' => 'required|string|max:255',
            'licencia_conducir' => 'nullable|string|max:5',
            'id_roles' => 'required|integer',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('admin.users')->with('success', 'Usuario añadido correctamente');
    }
}
