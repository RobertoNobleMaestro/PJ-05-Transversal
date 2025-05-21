<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Reserva;
use App\Models\VehiculosReservas;
use Illuminate\Http\Request;
use App\Models\Lugar;
use App\Models\Tipo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VehiculoCrudController extends Controller
{
    private function checkGestor($request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        if (auth()->user()->id_roles !== 3) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar
    }
    
    // Método para mostrar detalles de un vehículo (para clientes)
    public function detalle($id)
    {
        $vehiculo = Vehiculo::with(['tipo', 'lugar', 'caracteristicas', 'valoraciones', 'vehiculosReservas.reserva', 'imagenes'])
            ->findOrFail($id);
            
        // Cargamos explícitamente el parking para asegurarnos de tener la relación completa
        if ($vehiculo->parking_id) {
            $parking = \App\Models\Parking::find($vehiculo->parking_id);
        } else {
            $parking = null;
        }

        $precioUnitario = $vehiculo->vehiculosReservas
            ->where('fecha_final', '>=', now())
            ->first()->precio_unitario ?? $vehiculo->precio_unitario;
        
        // Solo usamos valores por defecto si realmente no hay parking o coordenadas
        if ($parking && is_numeric($parking->latitud) && is_numeric($parking->longitud)) {
            $latitud = (float)$parking->latitud;
            $longitud = (float)$parking->longitud;
        } else {
            // Coordenadas por defecto (Madrid)
            $latitud = 40.4168;
            $longitud = -3.7038;
        }

        return view('vehiculos.detalle_vehiculo', [
            'vehiculo' => $vehiculo,
            'precio_unitario' => $precioUnitario,
            'imagenes' => $vehiculo->imagenes,
            'parking' => $parking,
            'latitud' => $latitud,
            'longitud' => $longitud
        ]);
    }
    public function getVehiculos(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado'
                ], 401);
            }
    
            if (auth()->user()->id_roles !== 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tienes permiso para acceder a esta sección'
                ], 403);
            }
    
            $gestor = auth()->user();
    
            // Obtener el id_lugar del gestor a través de su parking
            $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();
    
            if (!$parking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se ha encontrado un lugar asignado a este gestor'
                ], 400);
            }
    
            $vehiculos = Vehiculo::select(
                    'vehiculos.*', 
                    'lugares.nombre as nombre_lugar', 
                    'tipo.nombre as nombre_tipo',
                    'parking.nombre as nombre_parking'
                )
                ->leftJoin('lugares', 'vehiculos.id_lugar', '=', 'lugares.id_lugar')
                ->leftJoin('tipo', 'vehiculos.id_tipo', '=', 'tipo.id_tipo')
                ->leftJoin('parking', 'vehiculos.parking_id', '=', 'parking.id')
                ->where('vehiculos.id_lugar', $parking->id_lugar)
                ->with(['imagenes'])
                ->withCount('reservas');
    
            // Aplicar filtros opcionales
            if ($request->filled('tipo')) {
                $vehiculos->where('vehiculos.id_tipo', $request->tipo);
            }
    
            if ($request->filled('marca')) {
                $vehiculos->where('vehiculos.marca', 'like', '%' . $request->marca . '%');
            }
    
            if ($request->filled('anio')) {
                $vehiculos->where('vehiculos.año', $request->anio);
            }
    
            // Filtro por parking
            if ($request->filled('parking_id')) {
                $vehiculos->where('vehiculos.parking_id', $request->parking_id);
            }
    
            // Paginación
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
    
            $paginated = $vehiculos->orderBy('vehiculos.id_vehiculos', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
    
            return response()->json([
                'vehiculos' => collect($paginated->items())->map(function ($vehiculo) {
                    $imagen = null;
                    if ($vehiculo->imagenes && $vehiculo->imagenes->count() > 0) {
                        $imagen = asset('img/vehiculos/' . $vehiculo->imagenes[0]->nombre_archivo);
                    }
                    return [
                        'id_vehiculos' => $vehiculo->id_vehiculos,
                        'marca' => $vehiculo->marca,
                        'modelo' => $vehiculo->modelo,
                        'año' => $vehiculo->año,
                        'kilometraje' => $vehiculo->kilometraje,
                        'precio' => $vehiculo->precio_dia,
                        'nombre_lugar' => $vehiculo->nombre_lugar ?? 'No asignado',
                        'nombre_tipo' => $vehiculo->nombre_tipo ?? 'No asignado',
                        'nombre_parking' => $vehiculo->nombre_parking ?? 'No asignado',
                        'tiene_reservas' => VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)->exists(),
                        'imagen' => $imagen,
                    ];
                }),
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem(),
                ]
            ]);
            

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar los vehículos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function index(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }
    
        $gestor = Auth::user();
    
        // Obtener el parking y lugar del gestor
        $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();
    
        if (!$parking) {
            return redirect()->back()->with('error', 'No se ha encontrado un parking asignado a este gestor.');
        }
    
        $lugarGestor = Lugar::find($parking->id_lugar); // ← Aquí obtenemos el nombre del lugar
    
        $vehiculos = Vehiculo::where('id_lugar', $parking->id_lugar)->get();
        $parkings = \App\Models\Parking::where('id_lugar', $parking->id_lugar)->get();
    
        $tipo = Tipo::all();
        $lugares = Lugar::all();
        $anios = Vehiculo::select('año')->distinct()->orderBy('año', 'desc')->pluck('año');
        $valoraciones = [1, 2, 3, 4, 5];
    
        return view('gestor.crudVehiculos', compact(
            'tipo',
            'lugares',
            'anios',
            'valoraciones',
            'vehiculos',
            'lugarGestor',
            'parkings'
        ));
    }
    

    public function create(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }

        $gestor = Auth::user();
        $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();

        if (!$parking) {
            return redirect()->back()->with('error', 'No se ha encontrado un parking asignado a este gestor.');
        }

        $lugares = Lugar::where('id_lugar', $parking->id_lugar)->get();
        $tipo = Tipo::all();
        $parkings = \App\Models\Parking::where('id_lugar', $parking->id_lugar)->get();

        return view('gestor.add_vehiculo', compact('lugares', 'tipo', 'parkings'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y')),
                'kilometraje' => 'required|integer|min:0',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipo,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'matricula' => 'nullable|string|max:20',
                'parking_id' => 'required|exists:parking,id',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096'
            ]);

            // Guardar el vehículo y obtener la instancia
            $vehiculo = Vehiculo::create($validatedData);
            
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    if ($imagen instanceof \Illuminate\Http\UploadedFile && $imagen->isValid()) {
                        $nombreArchivo = uniqid('vehiculo_', true) . '.' . $imagen->getClientOriginalExtension();
                        $imagen->move(public_path('img/vehiculos'), $nombreArchivo);

                        \App\Models\ImagenVehiculo::create([
                            'nombre_archivo' => $nombreArchivo,
                            'id_vehiculo' => $vehiculo->id_vehiculos
                        ]);
                    }
                }
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo añadido correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('gestor.vehiculos')->with('success', 'Vehículo añadido correctamente');

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
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }

        $vehiculo = Vehiculo::findOrFail($id_vehiculos);
        $gestor = Auth::user();
        $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();

        if (!$parking) {
            return redirect()->back()->with('error', 'No se ha encontrado un parking asignado a este gestor.');
        }

        $lugares = Lugar::where('id_lugar', $parking->id_lugar)->get();
        $tipo = Tipo::all();
        $parkings = \App\Models\Parking::where('id_lugar', $parking->id_lugar)->get();

        return view('gestor.edit_vehiculo', compact('vehiculo', 'lugares', 'tipo', 'parkings'));
    }
    public function getReservas($id)
    {
        $reservas = DB::table('vehiculos_reservas as vr')
            ->join('reservas as r', 'vr.id_reservas', '=', 'r.id_reservas')
            ->join('users as u', 'r.id_usuario', '=', 'u.id_usuario')
            ->where('vr.id_vehiculos', $id)
            ->select(
                'r.id_reservas as id_reserva',
                'vr.fecha_ini as fecha_inicio',
                'vr.fecha_final as fecha_fin',
                'u.nombre as cliente_nombre',
                'r.estado'
            )
            ->get();
    
        return response()->json(['reservas' => $reservas]);
    }
    
    
    public function update(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);

            // Eliminar imágenes anteriores de la base de datos
            $imagenesAnteriores = \App\Models\ImagenVehiculo::where('id_vehiculo', $vehiculo->id_vehiculos)->get();
            foreach ($imagenesAnteriores as $img) {
                // Eliminar archivo físico si existe
                $ruta = public_path('img/vehiculos/' . $img->nombre_archivo);
                if (file_exists($ruta)) {
                    @unlink($ruta);
                }
                $img->delete();
            }

            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kilometraje' => 'required|integer|min:0',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipo,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'matricula' => 'nullable|string|max:20',
                'parking_id' => 'required|exists:parking,id',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096'
            ]);

            $vehiculo->update($validatedData);
            
            // Ahora guarda las nuevas imágenes (si las hay)
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    if ($imagen instanceof \Illuminate\Http\UploadedFile && $imagen->isValid()) {
                        $nombreArchivo = uniqid('vehiculo_', true) . '.' . $imagen->getClientOriginalExtension();
                        $imagen->move(public_path('img/vehiculos'), $nombreArchivo);

                        \App\Models\ImagenVehiculo::create([
                            'nombre_archivo' => $nombreArchivo,
                            'id_vehiculo' => $vehiculo->id_vehiculos
                        ]);
                    }
                }
            }
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo actualizado correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('gestor.vehiculos')->with('success', 'Vehículo actualizado correctamente');
            
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
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }

        DB::beginTransaction(); // Iniciar la transacción

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);

            // Eliminar las características asociadas al vehículo
            DB::table('caracteristicas')->where('id_vehiculos', $id_vehiculos)->delete();

            // Eliminar las imágenes asociadas al vehículo
            DB::table('imagen_vehiculo')->where('id_vehiculo', $id_vehiculos)->delete();

            // Eliminar las reservas asociadas al vehículo
            DB::table('vehiculos_reservas')->where('id_vehiculos', $id_vehiculos)->delete();

            // Finalmente, eliminar el vehículo
            $vehiculo->delete();

            DB::commit(); // Confirmar la transacción

            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo eliminado correctamente'
                ], 200);
            }

            // Si es una petición tradicional, redireccionar
            return redirect()->route('gestor.vehiculos')->with('success', 'Vehículo eliminado correctamente');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error

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
