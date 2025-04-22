<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Lugar;
use App\Models\Tipo;
use Illuminate\Validation\ValidationException;

class VehiculoController extends Controller
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
    
    // Método para obtener vehiculos en formato JSON (para AJAX)
    public function getVehiculos(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
        
        // Iniciar la consulta
        $query = Vehiculo::select('vehiculos.*', 'lugares.nombre as nombre_lugar', 'tipos.nombre_tipo')
                    ->leftJoin('lugares', 'vehiculos.id_lugar', '=', 'lugares.id_lugar')
                    ->leftJoin('tipos', 'vehiculos.id_tipo', '=', 'tipos.id_tipo');
        
        // Aplicar filtros si existen
        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('vehiculos.id_tipo', $request->tipo);
        }
        
        if ($request->has('lugar') && !empty($request->lugar)) {
            $query->where('vehiculos.id_lugar', $request->lugar);
        }
        
        if ($request->has('marca') && !empty($request->marca)) {
            $query->where('vehiculos.marca', 'like', '%' . $request->marca . '%');
        }
        
        if ($request->has('anio') && !empty($request->anio)) {
            $query->where('vehiculos.año', $request->anio);
        }
        
        if ($request->has('valoracion') && !empty($request->valoracion)) {
            // Aquí asumimos que hay una relación con valoraciones y queremos filtrar por puntuación media
            $query->join('valoraciones', 'vehiculos.id_vehiculos', '=', 'valoraciones.id_vehiculos')
                  ->where('valoraciones.puntuacion', '>=', $request->valoracion)
                  ->groupBy(
                      'vehiculos.id_vehiculos', 'vehiculos.marca', 'vehiculos.modelo', 
                      'vehiculos.año', 'vehiculos.kilometraje', 'vehiculos.seguro_incluido', 
                      'vehiculos.id_lugar', 'vehiculos.id_tipo', 'vehiculos.precio_dia', 
                      'vehiculos.disponibilidad', 'lugares.nombre', 'tipos.nombre_tipo'
                  );
        }
        
        // Ejecutar la consulta
        $vehiculos = $query->get();
        
        // Calcular la valoración media para cada vehículo
        $vehiculos->each(function ($vehiculo) {
            try {
                // Crear una instancia del modelo para acceder al método getValoracionMedia
                $vehiculoModel = Vehiculo::find($vehiculo->id_vehiculos);
                $vehiculo->valoracion_media = $vehiculoModel ? $vehiculoModel->getValoracionMedia() : null;
            } catch (\Exception $e) {
                // Si hay algún error al calcular la valoración, establecerla como null
                $vehiculo->valoracion_media = null;
            }
        });
        
        return response()->json([
            'vehiculos' => $vehiculos
        ]);
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros
        $tipos = Tipo::all();
        $lugares = Lugar::all();
        
        // Obtener años únicos de vehículos para el filtro
        $anios = Vehiculo::select('año')->distinct()->orderBy('año', 'desc')->pluck('año');
        
        // Valoraciones posibles (1-5) para el filtro
        $valoraciones = [1, 2, 3, 4, 5];
        
        return view('admin.vehiculos', compact('tipos', 'lugares', 'anios', 'valoraciones'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        $lugares = Lugar::all();
        $tipos = Tipo::all();
        
        return view('admin.add_vehiculo', compact('lugares', 'tipos'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kilometraje' => 'required|integer|min:0',
                'seguro_incluido' => 'boolean',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipos,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'disponibilidad' => 'boolean',
            ]);

            // El seguro incluido viene como checkbox, así que si no está marcado, será null
            $validatedData['seguro_incluido'] = $request->has('seguro_incluido') ? true : false;
            $validatedData['disponibilidad'] = $request->has('disponibilidad') ? true : false;

            Vehiculo::create($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo añadido correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo añadido correctamente');

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al crear el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        $vehiculo = Vehiculo::findOrFail($id_vehiculos);
        $lugares = Lugar::all();
        $tipos = Tipo::all();
        
        return view('admin.edit_vehiculo', compact('vehiculo', 'lugares', 'tipos'));
    }

    public function update(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);
            
            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kilometraje' => 'required|integer|min:0',
                'seguro_incluido' => 'boolean',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipos,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'disponibilidad' => 'boolean',
            ]);

            // El seguro incluido viene como checkbox, así que si no está marcado, será null
            $validatedData['seguro_incluido'] = $request->has('seguro_incluido') ? true : false;
            $validatedData['disponibilidad'] = $request->has('disponibilidad') ? true : false;

            $vehiculo->update($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo actualizado correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo actualizado correctamente');
            
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);
            $vehiculo->delete();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo eliminado correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo eliminado correctamente');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }
}
