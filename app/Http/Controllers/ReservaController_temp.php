<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Reservas
 * 
 * Este controlador gestiona todas las operaciones relacionadas con las reservas:
 * - Listado, filtrado y búsqueda (con soporte AJAX)
 * - Creación y edición de reservas
 * - Asignación de vehículos a reservas
 * - Eliminación de reservas
 * 
 * Incluye métodos específicos para responder a peticiones AJAX desde el frontend.
 */
class ReservaController extends Controller
{
    /**
     * Verifica si el usuario tiene permisos de administrador
     * 
     * Este método privado se utiliza en varias acciones del controlador para:
     * 1. Verificar si el usuario está autenticado
     * 2. Comprobar si tiene rol de administrador (id_roles = 1)
     * 3. Responder adecuadamente según el tipo de petición (JSON o normal)
     * 
     * @param Request $request La petición HTTP actual
     * @return mixed Null si el usuario es admin, o una redirección/respuesta JSON si no lo es
     */
    private function checkAdmin($request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario tiene rol de administrador (id_roles = 1)
        if (auth()->user()->id_roles !== 1) {
            // Si es una petición AJAX (expectsJson()), devolver respuesta JSON con código 403
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            // Si es una carga normal de página, redirigir con mensaje flash
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar con la acción solicitada
    }
    
    /**
     * Obtiene reservas filtradas en formato JSON para peticiones AJAX
     * 
     * Este método es clave para la interacción AJAX entre el frontend y backend:
     * - Recibe peticiones desde el JavaScript (admin-reservas.js)
     * - Aplica filtros dinámicos según los parámetros enviados
     * - Devuelve datos en formato JSON para actualizar la interfaz sin recargar
     * 
     * FLUJO AJAX:
     * 1. El frontend hace fetch() a esta URL con parámetros de filtro
     * 2. Este método procesa la petición y consulta la base de datos
     * 3. Devuelve JSON que el frontend procesa para actualizar la tabla
     * 
     * @param Request $request La petición HTTP con los filtros a aplicar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos filtrados
     */
    public function getReservas(Request $request)
    {
        // Verificar permisos de administrador (devuelve respuesta si no es admin)
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
        
        // Iniciar la consulta con joins para obtener datos relacionados
        // Esto crea una única consulta SQL optimizada en lugar de múltiples consultas
        $query = Reserva::select('reservas.*', 'users.nombre as nombre_usuario', 'lugares.nombre as nombre_lugar')
                    ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                    ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
        
        // Aplicar filtros dinámicamente según los parámetros enviados desde el frontend
        // Cada filtro modifica la consulta SQL solo si el parámetro está presente
        if ($request->has('usuario') && !empty($request->usuario)) {
            $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
        }
        
        if ($request->has('lugar') && !empty($request->lugar)) {
            $query->where('reservas.id_lugar', $request->lugar);
        }
        
        if ($request->has('estado') && !empty($request->estado)) {
            $query->where('reservas.estado', $request->estado);
        }
        
        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->whereDate('reservas.fecha_reserva', $request->fecha);
        }
        
        // Ejecutar la consulta y obtener resultados
        $reservas = $query->get();
        
        // Cargar relaciones adicionales para cada reserva
        // Esto es un ejemplo de "carga diferida" (lazy loading) optimizada
        $reservas->each(function ($reserva) {
            // Para cada reserva, obtener sus vehículos asociados con datos específicos
            $reserva->vehiculos_info = $reserva->vehiculos()->select(
                'vehiculos.id_vehiculos', 
                'vehiculos.marca', 
                'vehiculos.modelo', 
                'vehiculos_reservas.fecha_ini',
                'vehiculos_reservas.fecha_final',
                'vehiculos_reservas.precio_unitario'
            )->get();
        });
        
        // Devolver respuesta JSON que será procesada por el JavaScript en el frontend
        // En admin-reservas.js, la función fetch procesará esta respuesta
        return response()->json([
            'reservas' => $reservas
        ]);
    }

    /**
     * Muestra la página principal de gestión de reservas
     * 
     * Este método:
     * 1. Verifica permisos de administrador
     * 2. Carga datos necesarios para los filtros (usuarios, lugares)
     * 3. Carga la vista con esos datos iniciales
     * 
     * La vista luego cargará admin-reservas.js que hará peticiones AJAX
     * a getReservas() para mostrar y filtrar datos dinámicamente.
     * 
     * @param Request $request La petición HTTP actual
     * @return \Illuminate\View\View Vista con datos para filtros
     */
    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros de la interfaz
        $usuarios = User::select('id_usuario', 'nombre')->get();
        $lugares = Lugar::select('id_lugar', 'nombre')->get();
        
        // Cargar vista con datos iniciales para filtros
        // La carga de reservas se hará vía AJAX con admin-reservas.js
        return view('admin.reservas.index', compact('usuarios', 'lugares'));
    }

    /**
     * Muestra el formulario para crear una nueva reserva
     * 
     * Este método prepara los datos necesarios para mostrar el formulario
     * de creación de reservas en el panel de administración.
     * 
     * @param Request $request La petición HTTP actual
     * @return \Illuminate\View\View Vista con datos para el formulario
     */
    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener usuarios, lugares y vehículos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::where('disponibilidad', true)->get();
        
        return view('admin.add_reserva', compact('usuarios', 'lugares', 'vehiculos'));
    }

    /**
     * Almacena una nueva reserva en la base de datos
     * 
     * Este método procesa los datos enviados desde el formulario de creación
     * y crea una nueva reserva con sus vehículos asociados. Utiliza transacciones
     * de base de datos para garantizar la integridad de los datos.
     * 
     * FUNCIONAMIENTO AJAX:
     * Cuando el formulario se envía desde admin-add-reserva.js:
     * 1. JavaScript recoge todos los datos del formulario usando FormData
     * 2. Envía una petición POST vía fetch() a esta ruta
     * 3. Este método procesa y valida los datos
     * 4. Devuelve una respuesta JSON que el frontend utiliza para mostrar
     *    mensajes de éxito/error y redirigir si es necesario
     * 
     * @param Request $request La petición HTTP con los datos del formulario
     * @return \Illuminate\Http\JsonResponse|Redirect Respuesta JSON o redirección
     */
    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            // Validar los datos del formulario antes de procesarlos
            // Esta validación es crucial tanto para seguridad como para
            // garantizar la integridad de los datos
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Comenzar una transacción para asegurar la integridad de los datos
            // Si algo falla durante el proceso, todos los cambios se revierten
            DB::beginTransaction();
            
            // Crear la reserva principal con precio inicial 0
            $reserva = Reserva::create([
                'fecha_reserva' => $validatedData['fecha_reserva'],
                'total_precio' => 0, // Se actualizará después
                'estado' => $validatedData['estado'],
                'id_lugar' => $validatedData['id_lugar'],
                'id_usuario' => $validatedData['id_usuario'],
            ]);
            
            // Asociar vehículos a la reserva y calcular el precio total
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva (tabla pivot vehiculos_reservas)
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->update(['total_precio' => $total_precio]);
            
            // Confirmar la transacción
            DB::commit();
            
            // RESPUESTA AJAX: Si la petición espera JSON, devolver respuesta JSON
            // Esta respuesta la procesará el método fetch() en admin-add-reserva.js
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva añadida correctamente'
                ], 200);
            }
            
            // Si no es AJAX, redirigir con mensaje flash
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva añadida correctamente');
            
        } catch (ValidationException $e) {
            // Si hay problemas de validación, revertir transacción 
            DB::rollBack();
            
            // RESPUESTA AJAX: Devolver errores de validación en formato JSON
            // El frontend mostrará estos errores junto a los campos correspondientes
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Si no es AJAX, redirigir con errores
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // Si hay cualquier otro error, revertir transacción
            DB::rollBack();
            
            // Registrar error para depuración
            \Log::error('Error creando reserva: ' . $e->getMessage());
            
            // RESPUESTA AJAX: Devolver mensaje de error genérico
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            // Si no es AJAX, redirigir con mensaje de error
            return redirect()->back()->with('error', 'Error al crear la reserva: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario para editar una reserva existente
     * 
     * Este método prepara los datos necesarios para el formulario de edición,
     * cargando tanto los datos básicos de la reserva como sus vehículos asociados.
     * 
     * @param Request $request La petición HTTP actual
     * @param int $id_reservas ID de la reserva a editar
     * @return \Illuminate\View\View Vista con datos para el formulario
     */
    public function edit(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Buscar la reserva por su ID
        $reserva = Reserva::findOrFail($id_reservas);
        
        // Obtener los vehículos asociados a esta reserva con sus datos de relación
        // Esto es importante para mostrar los detalles específicos de cada vehículo
        // como fechas y precios unitarios en el formulario de edición
        $reserva_vehiculos = $reserva->vehiculos()->select(
            'vehiculos.id_vehiculos',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos_reservas.fecha_ini',
            'vehiculos_reservas.fecha_final',
            'vehiculos_reservas.precio_unitario'
        )->get();
        
        // Obtener datos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::all(); // Mostrar todos los vehículos para poder editarlos
        
        return view('admin.edit_reserva', compact('reserva', 'reserva_vehiculos', 'usuarios', 'lugares', 'vehiculos'));
    }
    
    /**
     * Actualiza una reserva existente con los datos enviados
     * 
     * FUNCIONAMIENTO AJAX:
     * 1. El formulario en admin-edit-reserva.js recopila todos los datos 
     * 2. JavaScript envía una petición AJAX (fetch) a esta ruta
     * 3. Este método procesa los datos, valida y actualiza en la base de datos
     * 4. Devuelve una respuesta JSON con estado de éxito o error
     * 5. El frontend actualiza la interfaz según el resultado
     * 
     * La actualización utiliza transacciones de base de datos para garantizar
     * que todas las operaciones se completen o se reviertan en caso de error.
     * 
     * @param Request $request La petición HTTP con los datos del formulario
     * @param int $id_reservas ID de la reserva a actualizar
     * @return \Illuminate\Http\JsonResponse|Redirect Respuesta JSON o redirección
     */
    public function update(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            // Validar todos los campos del formulario
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Encontrar la reserva a actualizar
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            // Si cualquier parte del proceso falla, todos los cambios se revierten
            DB::beginTransaction();
            
            // Actualizar los datos básicos de la reserva
            $reserva->fecha_reserva = $validatedData['fecha_reserva'];
            $reserva->estado = $validatedData['estado'];
            $reserva->id_lugar = $validatedData['id_lugar'];
            $reserva->id_usuario = $validatedData['id_usuario'];
            
            // Eliminar todas las relaciones existentes con vehículos
            // En la tabla pivot vehiculos_reservas usando el método detach()
            $reserva->vehiculos()->detach();
            
            // Asociar vehículos a la reserva (reemplazando los anteriores)
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días (igual que en crear reserva)
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva con los datos actualizados
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->total_precio = $total_precio;
            $reserva->save();
            
            // Confirmar la transacción (aplicar todos los cambios)
            DB::commit();
            
            // RESPUESTA AJAX: Si la petición espera JSON, devolver respuesta JSON
            // Esta respuesta la procesará el método fetch() en admin-edit-reserva.js
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva actualizada correctamente'
                ], 200);
            }
            
            // Si no es AJAX, redirigir con mensaje flash
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva actualizada correctamente');
            
        } catch (ValidationException $e) {
            // Si hay errores de validación
            DB::rollBack();
            
            // RESPUESTA AJAX: Devolver errores de validación en formato JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Si no es AJAX, redirigir con errores
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // Si ocurre cualquier otro error
            DB::rollBack();
            
            // RESPUESTA AJAX: Devolver mensaje de error general
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            // Si no es AJAX, redirigir con mensaje de error
            return redirect()->back()->with('error', 'Error al actualizar la reserva: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Elimina una reserva de la base de datos
     * 
     * Esta función es especialmente importante para entender las peticiones AJAX:
     * 
     * FLUJO DE ELIMINACIÓN VÍA AJAX:
     * 1. En admin-reservas.js, cuando el usuario hace clic en "Eliminar", 
     *    se muestra un diálogo de confirmación con SweetAlert2
     * 2. Si confirma, JavaScript envía una petición fetch() a esta ruta con método DELETE
     * 3. Este método procesa la solicitud y elimina la reserva
     * 4. Devuelve una respuesta JSON con éxito o error
     * 5. JavaScript recibe la respuesta y actualiza la interfaz sin recargar la página:
     *    - Si hay éxito, elimina la fila de la tabla
     *    - Si hay error, muestra mensaje de error
     * 
     * @param Request $request La petición HTTP
     * @param int $id_reservas ID de la reserva a eliminar
     * @return \Illuminate\Http\JsonResponse|Redirect Respuesta JSON o redirección
     */
    public function destroy(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }

        try {
            // Encontrar la reserva a eliminar
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Eliminar la reserva (y sus relaciones con vehículos por cascada)
            // Esto también eliminará automáticamente las entradas en vehiculos_reservas
            // gracias a las restricciones de clave foránea con onDelete('cascade')
            $reserva->delete();
            
            // Confirmar la transacción
            DB::commit();
            
            // RESPUESTA AJAX: Devolver JSON para actualizar la interfaz sin recargar
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva eliminada correctamente'
                ], 200);
            }
            
            // Si no es AJAX, redirigir con mensaje flash
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva eliminada correctamente');
            
        } catch (\Exception $e) {
            // Si ocurre cualquier error, revertir transacción
            DB::rollBack();
            
            // RESPUESTA AJAX: Devolver mensaje de error
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            // Si no es AJAX, redirigir con mensaje de error
            return redirect()->route('admin.reservas.index')->with('error', 'Error al eliminar la reserva: ' . $e->getMessage());
        }
    }
}
