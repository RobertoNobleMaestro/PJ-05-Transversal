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
        
        // Iniciar consulta
        $query = Lugar::query();
        
        // Aplicar filtros si existen
        if ($request->has('nombre') && !empty($request->nombre)) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        
        if ($request->has('direccion') && !empty($request->direccion)) {
            $query->where('direccion', 'like', '%' . $request->direccion . '%');
        }
        
        // Ejecutar la consulta
        $lugares = $query->get();
        
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
            
            // Obtener todos los IDs de vehículos asociados a este lugar
            $vehiculosIds = $lugar->vehiculos()->pluck('id_vehiculos');
            
            // Detectar la acción a realizar (por defecto es eliminar)
            $accion = $request->input('accion', 'eliminar');
            
            // Obtener IDs de reservas asociadas al lugar
            $reservasIds = $lugar->reservas()->pluck('id_reservas');
            
            // 1. Primero eliminar vehiculos_reservas que depende tanto de reservas como de vehículos
            if (count($reservasIds) > 0) {
                DB::table('vehiculos_reservas')
                    ->whereIn('id_reservas', $reservasIds)
                    ->delete();
            }
            
            if (count($vehiculosIds) > 0) {
                DB::table('vehiculos_reservas')
                    ->whereIn('id_vehiculos', $vehiculosIds)
                    ->delete();
            }
            
            if (count($reservasIds) > 0) {
                // 2. Eliminar valoraciones que dependen de reservas
                DB::table('valoraciones')
                    ->whereIn('id_reservas', $reservasIds)
                    ->delete();
                
                // Obtener IDs de pagos asociados a las reservas
                $pagosIds = DB::table('pago')
                    ->whereIn('id_reservas', $reservasIds)
                    ->pluck('id_pago');
                
                // 3. Eliminar métodos de pago que dependen de pagos
                if (count($pagosIds) > 0) {
                    DB::table('metodos_de_pago')
                        ->whereIn('id_pago', $pagosIds)
                        ->delete();
                }
                
                // 4. Eliminar pagos asociados a las reservas
                DB::table('pago')
                    ->whereIn('id_reservas', $reservasIds)
                    ->delete();
            }
            
            // 5. Ahora podemos eliminar reservas con seguridad
            $lugar->reservas()->delete();
            
            // Procesar según la acción seleccionada
            if ($accion === 'eliminar') {
                // Eliminar todo (comportamiento original)
                if (count($vehiculosIds) > 0) {
                    // 6. Eliminar imágenes de vehículos
                    DB::table('imagen_vehiculo')
                        ->whereIn('id_vehiculo', $vehiculosIds)
                        ->delete();
                    
                    // 7. Eliminar características de los vehículos
                    DB::table('caracteristicas')
                        ->whereIn('id_vehiculos', $vehiculosIds)
                        ->delete();
                    
                    // 8. Eliminar cualquier otra relación posible
                    $tablas = [
                        'valoraciones_vehiculos',
                        'vehiculos_mantenimiento',
                        'vehiculos_seguros',
                        'pagos_vehiculos',
                        'vehiculos_servicios'
                    ];
                    
                    foreach ($tablas as $tabla) {
                        if (DB::getSchemaBuilder()->hasTable($tabla)) {
                            DB::table($tabla)
                                ->whereIn('id_vehiculos', $vehiculosIds)
                                ->delete();
                        }
                    }
                }
                
                // 9. Eliminar vehículos asociados al lugar
                $lugar->vehiculos()->delete();
                
                // 10. Finalmente eliminar el lugar
                $lugar->delete();
                
                $mensaje = 'Lugar y todos sus elementos asociados eliminados correctamente';
                
            } elseif ($accion === 'reubicar') {
                // Validar que existe un lugar de destino
                $lugarDestinoId = $request->input('lugar_destino_id');
                if (!$lugarDestinoId) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Debe seleccionar un lugar de destino para la reubicación'
                    ], 422);
                }
                
                // Verificar que el lugar de destino existe
                $lugarDestino = Lugar::findOrFail($lugarDestinoId);
                
                // Actualizar los vehículos para asignarles el nuevo lugar
                if (count($vehiculosIds) > 0) {
                    DB::table('vehiculos')
                        ->whereIn('id_vehiculos', $vehiculosIds)
                        ->update(['id_lugar' => $lugarDestinoId]);
                }
                
                // Eliminar el lugar original
                $lugar->delete();
                
                $mensaje = "Lugar eliminado correctamente. Vehículos reubicados a '{$lugarDestino->nombre}'";
                
            } elseif ($accion === 'nuevo') {
                // Validar datos del nuevo lugar
                $nuevoLugarData = $request->input('nuevo_lugar');
                if (!$nuevoLugarData || !isset($nuevoLugarData['nombre']) || !isset($nuevoLugarData['direccion']) || 
                    !isset($nuevoLugarData['latitud']) || !isset($nuevoLugarData['longitud'])) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Información incompleta para crear el nuevo lugar'
                    ], 422);
                }
                
                // Crear el nuevo lugar
                $nuevoLugar = Lugar::create([
                    'nombre' => $nuevoLugarData['nombre'],
                    'direccion' => $nuevoLugarData['direccion'],
                    'latitud' => $nuevoLugarData['latitud'],
                    'longitud' => $nuevoLugarData['longitud']
                ]);
                
                // Actualizar los vehículos para asignarles el nuevo lugar
                if (count($vehiculosIds) > 0) {
                    DB::table('vehiculos')
                        ->whereIn('id_vehiculos', $vehiculosIds)
                        ->update(['id_lugar' => $nuevoLugar->id_lugar]);
                }
                
                // Eliminar el lugar original
                $lugar->delete();
                
                $mensaje = "Lugar eliminado correctamente. Vehículos reubicados al nuevo lugar '{$nuevoLugar->nombre}'";
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Acción no reconocida'
                ], 422);
            }
            
            // Si todo salió bien, confirmar la transacción
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $mensaje
            ], 200);
            
        } catch (\Exception $e) {
            // Si algo falló, revertir la transacción
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la operación: ' . $e->getMessage()
            ], 500);
        }
    }
}
