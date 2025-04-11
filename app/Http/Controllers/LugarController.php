<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lugar;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LugarController extends Controller
{
    // Método privado para verificar si el usuario es administrador
    private function checkAdmin($request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario tiene rol de administrador (id_roles = 1)
        if (auth()->user()->id_roles !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar
    }
    
    // Método para obtener lugares en formato JSON (para AJAX)
    public function getLugares(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
        
        // Cargar lugares
        $lugares = Lugar::all();
        
        return response()->json([
            'lugares' => $lugares
        ]);
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Cargar lugares
        $lugares = Lugar::all();

        return view('admin.lugares', compact('lugares'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        return view('admin.add_lugar');
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
            ]);

            Lugar::create($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lugar añadido correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.lugares')->with('success', 'Lugar añadido correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el lugar',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, $id_lugar)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        $lugar = Lugar::findOrFail($id_lugar);
        return view('admin.edit_lugar', compact('lugar'));
    }

    public function update(Request $request, $id_lugar)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            // Obtener datos dependiendo del Content-Type
            $data = $request->all();
            
            $lugar = Lugar::findOrFail($id_lugar);
            
            $validatedData = validator($data, [
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
            ])->validate();

            $lugar->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Lugar actualizado correctamente'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el lugar: ' . $e->getMessage(),
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id_lugar)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            if ($request->expectsJson()) {
                return $authCheck;
            }
            return $authCheck;
        }
        
        try {
            // Comenzar transacción
            DB::beginTransaction();
            
            // Obtener el lugar con sus relaciones
            $lugar = Lugar::with(['vehiculos', 'reservas'])->findOrFail($id_lugar);
            
            // Eliminar reservas asociadas al lugar
            $lugar->reservas()->delete();
            
            // Eliminar vehículos asociados al lugar
            $lugar->vehiculos()->delete();
            
            // Finalmente eliminar el lugar
            $lugar->delete();
            
            // Si todo salió bien, confirmar la transacción
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lugar y todos sus elementos asociados eliminados correctamente'
            ], 200);
        } catch (\Exception $e) {
            // Si algo falló, revertir la transacción
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el lugar: ' . $e->getMessage(),
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
