<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class AsalariadosController extends Controller
{
    /**
     * Muestra la lista de asalariados para el administrador financiero
     */
    public function index()
    {
        // Verificar que el usuario es un administrador financiero
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        // A partir de ahora, un admin financiero gestiona TODOS los asalariados
        // Obtener todos los lugares para el filtro
        $lugares = Lugar::all();
        
        // Obtener todos los roles para el filtro
        $roles = Role::all()->pluck('nombre_rol');
        
        // Obtener todos los parkings para el filtro
        $parkings = Parking::all();
        
        return view('admin_financiero.asalariados.index', [
            'lugares' => $lugares,
            'roles' => $roles,
            'parkings' => $parkings,
        ]);
    }
    
    /**
     * Obtiene los datos de asalariados para la tabla AJAX
     */
    public function getAsalariados(Request $request)
    {
        try {
            // Registro para depuración - Info de la petición
            \Log::info('Petición de filtrado recibida', [
                'parametros' => $request->all(),
                'user_agent' => $request->header('User-Agent')
            ]);
            
            // Iniciar la consulta base con todas las relaciones necesarias
            $query = Asalariado::with(['usuario', 'sede', 'parking'])
                ->where('estado', 'alta'); // Solo los que están de alta
            
            // Aplicar filtros si están presentes
            if ($request->has('nombre') && $request->nombre) {
                try {
                    $nombreBusqueda = trim($request->nombre);
                    \Log::info('Aplicando filtro por nombre: ' . $nombreBusqueda);
                    
                    // Verificamos la estructura de la tabla antes de aplicar el filtro
                    $query->whereHas('usuario', function($q) use ($nombreBusqueda) {
                        $q->where('nombre', 'like', "%{$nombreBusqueda}%")
                          ->orWhere('email', 'like', "%{$nombreBusqueda}%");
                    });
                } catch (\Exception $e) {
                    \Log::error('Error en filtro por nombre: ' . $e->getMessage());
                    // Continuamos con la ejecución a pesar del error
                }
            }
            
            if ($request->has('lugar') && $request->lugar) {
                try {
                    \Log::info('Aplicando filtro por lugar: ' . $request->lugar);
                    $query->where('id_lugar', $request->lugar);
                } catch (\Exception $e) {
                    \Log::error('Error en filtro por lugar: ' . $e->getMessage());
                }
            }
            
            if ($request->has('parking') && $request->parking) {
                try {
                    \Log::info('Aplicando filtro por parking: ' . $request->parking);
                    $query->where('parking_id', $request->parking);
                } catch (\Exception $e) {
                    \Log::error('Error en filtro por parking: ' . $e->getMessage());
                }
            }
            
            // Paginación
            $perPage = (int)$request->input('perPage', 10);
            $page = (int)$request->input('page', 1);
            
            // Verificar que los valores sean válidos
            if ($perPage <= 0) $perPage = 10;
            if ($page <= 0) $page = 1;
            
            // Obtenemos el total sin aplicar paginación
            $totalRegistros = $query->count();
            \Log::info('Total de registros encontrados: ' . $totalRegistros);
            
            // Aplicamos paginación
            $asalariados = $query->skip(($page - 1) * $perPage)
                                ->take($perPage)
                                ->get();
            
            // Cargamos la información de roles manualmente para evitar problemas
            foreach ($asalariados as $asalariado) {
                if ($asalariado->usuario) {
                    // Si el usuario tiene un id_roles, cargamos el rol desde la base de datos
                    if ($asalariado->usuario->id_roles) {
                        try {
                            $role = \App\Models\Role::find($asalariado->usuario->id_roles);
                            if ($role) {
                                // Asignamos el rol manualmente
                                $asalariado->usuario->role = $role;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Error al cargar rol para usuario ID ' . $asalariado->id_usuario . ': ' . $e->getMessage());
                        }
                    }
                }
            }
            
            // Calcular número total de páginas
            $totalPaginas = ceil($totalRegistros / $perPage);
            
            // Calcular estadísticas para las tarjetas de resumen
            try {
                $allAsalariados = Asalariado::where('estado', 'alta')->get();
                $totalSalarios = $allAsalariados->sum('salario');
                $avgSalario = $allAsalariados->count() > 0 ? $allAsalariados->avg('salario') : 0;
            } catch (\Exception $e) {
                \Log::error('Error al calcular estadísticas: ' . $e->getMessage());
                $totalSalarios = 0;
                $avgSalario = 0;
            }
            
            $response = [
                'asalariados' => $asalariados,
                'pagination' => [
                    'total' => $totalRegistros,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $totalPaginas
                ],
                'summary' => [
                    'total' => $totalRegistros,
                    'totalSalarios' => $totalSalarios,
                    'avgSalario' => $avgSalario
                ]
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('Error general en getAsalariados: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error al procesar la solicitud',
                'message' => 'Se ha producido un error al procesar la solicitud. Por favor, inténtelo de nuevo.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Muestra el formulario para editar un asalariado
     */
    public function edit($id)
    {
        $asalariado = Asalariado::with(['usuario', 'usuario.role', 'sede', 'parking'])->findOrFail($id);
        
        // Obtener todos los lugares para seleccionar
        $lugares = Lugar::all();
        
        // Obtener todos los parkings
        $parkings = Parking::all();
        
        return view('admin_financiero.asalariados.edit', [
            'asalariado' => $asalariado,
            'lugares' => $lugares,
            'parkings' => $parkings
        ]);
    }
    
    /**
     * Actualiza la información de un asalariado
     */
    public function update(Request $request, $id)
    {
        // Verificar que el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para esta acción.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        // Validar la solicitud
        try {
            $validatedData = $request->validate([
                'salario' => 'required|numeric|min:0',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'parking_id' => 'required|exists:parking,id',
                'hiredate' => 'required|date_format:Y-m-d'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error de validación.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $asalariado = Asalariado::with('usuario.role')->findOrFail($id);
            
            $asalariado->id_lugar = $validatedData['id_lugar'];
            $asalariado->parking_id = $validatedData['parking_id'];
            $asalariado->salario = $validatedData['salario'];

            $newHireDate = \Carbon\Carbon::parse($validatedData['hiredate'])->startOfDay();
            $currentHireDate = \Carbon\Carbon::parse($asalariado->hiredate)->startOfDay();

            if (!$newHireDate->eq($currentHireDate)) {
                $asalariado->hiredate = $newHireDate->format('Y-m-d');
                $today = \Carbon\Carbon::today();
                if ($newHireDate->gt($today)) {
                    $asalariado->dias_trabajados = 0;
                } else {
                    $calendarDaysSinceNewHire = $newHireDate->diffInDays($today) + 1;
                    $fullWeeks = floor($calendarDaysSinceNewHire / 7);
                    $remainingDays = $calendarDaysSinceNewHire % 7;
                    $asalariado->dias_trabajados = ($fullWeeks * 6) + min($remainingDays, 6);
                }
            }

            $asalariado->save();
            
            $mensaje = 'Información del asalariado actualizada correctamente.';

            if ($asalariado->usuario && $asalariado->usuario->role) {
                $rolId = $asalariado->usuario->role->id;
                $asalariadosMismoRol = Asalariado::whereHas('usuario', function($query) use ($rolId) {
                    $query->where('id_roles', $rolId);
                })->where('id', '!=', $asalariado->id) // No actualizar el mismo empleado dos veces
                  ->get();
                
                foreach ($asalariadosMismoRol as $asalariadoRol) {
                    $asalariadoRol->salario = $validatedData['salario'];
                    $asalariadoRol->save();
                }
                $mensaje = 'Información y salario actualizados para el empleado y todos los demás con el mismo rol.';
            }
            
            DB::commit();

            // No syntax error here
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $mensaje, 'asalariado_id' => $asalariado->id]);
            }
            return redirect()->route('admin.asalariados.index')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar el asalariado: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar el asalariado: ' . $e->getMessage());
        }
    }
    /**
     * Muestra el detalle de un asalariado.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $asalariado = Asalariado::with(['usuario', 'usuario.role', 'sede', 'parking'])->findOrFail($id);
        
        // Calcular días trabajados en el mes actual
        $calculoMesActual = $asalariado->calcularSalarioProporcional(); // Assumes $fecha = null for current month
        $diasTrabajadosMesActual = $calculoMesActual['diasTrabajados'];

        return view('admin_financiero.asalariados.show', [
            'asalariado' => $asalariado,
            'diasTrabajadosMesActual' => $diasTrabajadosMesActual
        ]);
    }
    
    /**
     * Genera la nómina para un asalariado y la ofrece para descarga en PDF.
     * También registra el gasto de la nómina si es el día 1 del mes y no existe ya.
     *
     * @param int $id El ID del asalariado.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function generarNomina($id)
    {
        // Obtener el asalariado con todas las relaciones necesarias
        $asalariado = Asalariado::with(['usuario', 'usuario.role', 'parking', 'sede'])->findOrFail($id);
        
        // Verificar permisos
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        $hoy = Carbon::now();
        $mesActual = $hoy->month;
        $anioActual = $hoy->year;
        $esDiaPrimero = $hoy->day == 1;
        $esVisualizacionMesActual = !$esDiaPrimero;
        
        // Verificar si ya existe un pago de nómina para el mes actual
        $nombreEmpleado = $asalariado->usuario ? $asalariado->usuario->nombre : 'empleado';
        $existeNominaDelMes = DB::table('gastos')
            ->where('id_asalariado', $asalariado->id)
            ->where('concepto', 'like', 'Nómina - ' . $nombreEmpleado . '%')
            ->whereYear('fecha', $anioActual)
            ->whereMonth('fecha', $mesActual)
            ->exists();
            
        // Asegurarse de que hiredate es un objeto Carbon antes de realizar cálculos
        if (is_string($asalariado->hiredate)) {
            $asalariado->hiredate = \Carbon\Carbon::parse($asalariado->hiredate);
        }
        
        // Calcular el salario, independientemente de si se va a guardar o solo visualizar
        if ($asalariado->estado == 'alta') {
            // Calcular salario proporcional para empleados activos
            $datosSalario = $asalariado->calcularSalarioProporcional();
        } else {
            // Calcular salario proporcional para empleados en proceso de baja
            $datosSalario = $asalariado->calcularSalarioBaja();
        }
        
        // Extraer los datos del salario
        $salarioBruto = isset($datosSalario['salarioProporcional']) ? $datosSalario['salarioProporcional'] : $datosSalario['salario'];
        $diasTrabajados = $datosSalario['diasTrabajados'];
        $diasEnMes = isset($datosSalario['diasLaborablesPotencialesEnMes']) ? $datosSalario['diasLaborablesPotencialesEnMes'] : $datosSalario['diasEnMes'];
        $porcentajeSalario = $datosSalario['porcentaje'];
        $salarioBase = $asalariado->salario; // Salario base completo
        
        // Calcular impuestos (simplificado)
        $impuestoRenta = $salarioBruto * 0.15; // 15% de IRPF
        $seguridadSocial = $salarioBruto * 0.065; // 6.5% de Seguridad Social
        $salarioNeto = $salarioBruto - $impuestoRenta - $seguridadSocial;
        
        // Datos para la nómina
        $data = [
            'asalariado' => $asalariado,
            'fecha' => $hoy->format('d/m/Y'),
            'periodo' => $hoy->format('F Y'),
            'salarioBruto' => $salarioBruto,
            'salarioBase' => $salarioBase,
            'diasTrabajados' => $diasTrabajados,
            'diasEnMes' => $diasEnMes,
            'porcentajeSalario' => $porcentajeSalario,
            'impuestoRenta' => $impuestoRenta,
            'seguridadSocial' => $seguridadSocial,
            'salarioNeto' => $salarioNeto,
            'empresa' => 'CarFlow S.L.',
            'direccionEmpresa' => 'Avenida Diagonal 123, Barcelona',
            'cifEmpresa' => 'B-12345678',
            'esPreview' => $esVisualizacionMesActual && !$existeNominaDelMes
        ];
        
        // Solo registrar el pago si es el día 1 y no existe ya un pago para este mes
        if ($esDiaPrimero && !$existeNominaDelMes) {
            // Registrar el pago en la base de datos
            DB::table('gastos')->insert([
                'id_asalariado' => $asalariado->id,
                'concepto' => 'Nómina - ' . $nombreEmpleado,
                'categoria' => 'Salarios',
                'tipo' => 'Salario',
                'importe' => $salarioBruto,
                'fecha' => $hoy,
                'created_at' => $hoy,
                'updated_at' => $hoy
            ]);
            
            // Si está en proceso de baja, cambiar estado a 'baja' definitivamente
            if ($asalariado->estado == 'baja_pendiente') {
                $asalariado->estado = 'baja';
                $asalariado->save();
            }
        }
        
        // Generar PDF
        $pdf = PDF::loadView('admin_financiero.nomina_pdf', $data);
        
        // Nombre del archivo con protección contra null
        $nombreEmpleado = $asalariado->usuario ? $asalariado->usuario->nombre : 'empleado';
        $nombreArchivo = 'nomina_' . $nombreEmpleado . '_' . $hoy->format('Y_m_d') . '.pdf';
        
        // Descargar PDF
        return $pdf->download($nombreArchivo);
    }
    
    /**
     * Programar la baja de un asalariado para el día 1 del mes siguiente
     */
    public function darDeBaja(Request $request, $id)
    {
        // Verificar que el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        $asalariado = Asalariado::findOrFail($id);
        
        if ($asalariado->estado == 'baja') {
            return redirect()->back()->with('error', 'Este asalariado ya está dado de baja');
        }
        
        if ($asalariado->estado_baja_programada == 'pendiente') {
            return redirect()->back()->with('error', 'Este asalariado ya tiene una baja programada');
        }
        
        try {
            // Calcular la fecha para el día 1 del mes siguiente
            $fechaActual = Carbon::now();
            $fechaBajaProgramada = $fechaActual->copy()->addMonth()->startOfMonth();
            
            // Registrar la fecha programada de baja
            $asalariado->fecha_baja_programada = $fechaBajaProgramada;
            $asalariado->estado_baja_programada = 'pendiente';
            
            // Mantener el estado actual como activo hasta la fecha programada
            $asalariado->save();
            
            // Formatear fecha para mostrar en mensaje
            $fechaFormateada = $fechaBajaProgramada->format('d/m/Y');
            
            // Registrar la acción para auditoría
            \Log::info('Baja programada para el asalariado ID ' . $id . ' con fecha: ' . $fechaFormateada);
            
            return redirect()->back()->with('success', 'Baja programada correctamente para el día ' . $fechaFormateada);
        } catch (\Exception $e) {
            \Log::error('Error al programar la baja del asalariado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al programar la baja: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra la lista de asalariados dados de baja
     */
    public function bajas()
    {
        // Verificar que el usuario es un administrador financiero
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        // Obtener todos los asalariados dados de baja
        $asalariados = Asalariado::with(['usuario', 'usuario.role', 'sede', 'parking'])
                               ->where('estado', 'baja')
                               ->get();
        
        return view('admin_financiero.asalariados.bajas', [
            'asalariados' => $asalariados
        ]);
    }
    
    /**
     * Dar de alta a un asalariado que estaba de baja
     */
    public function darDeAlta(Request $request, $id)
    {
        // Verificar que el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        try {
            $asalariado = Asalariado::findOrFail($id);
            
            if ($asalariado->estado == 'alta') {
                return redirect()->back()->with('error', 'Este asalariado ya está dado de alta');
            }
            
            // Obtener los días trabajados acumulados anteriormente
            $diasTrabajadosAcumulados = $asalariado->dias_trabajados ?? 0;
            
            // Actualizar estado y fecha de contratación, pero mantener los días trabajados acumulados
            $asalariado->estado = 'alta';
            $asalariado->hiredate = Carbon::now(); // Nueva fecha de contratación
            
            // Guardar los días acumulados en un campo adicional para referencia histórica si es necesario
            $asalariado->dias_acumulados_historico = $diasTrabajadosAcumulados;
            
            // Establecer la fecha de última reactivación
            $asalariado->fecha_ultima_reactivacion = Carbon::now();
            
            // Mantener los días trabajados acumulados (no se reinician)
            $asalariado->dias_trabajados = $diasTrabajadosAcumulados;
            
            $asalariado->save();
            
            // Registrar la acción para auditoría
            \Log::info('Asalariado ID ' . $id . ' dado de alta. Días trabajados preservados: ' . $diasTrabajadosAcumulados);
            
            return redirect()->route('admin.asalariados.index')
                ->with('success', 'Asalariado dado de alta correctamente. Días trabajados acumulados: ' . $diasTrabajadosAcumulados);
        } catch (\Exception $e) {
            \Log::error('Error al dar de alta asalariado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al procesar el alta: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el formulario para crear un nuevo asalariado
     */
    public function create()
    {
        // Verificar que el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        // Obtener todos los usuarios que no son asalariados
        $usuarios = User::whereDoesntHave('asalariado')->get();
        
        // Obtener todos los lugares
        $lugares = Lugar::all();
        
        // Obtener todos los parkings
        $parkings = Parking::all();
        
        // Obtener todos los roles
        $roles = Role::all();
        
        return view('admin_financiero.asalariados.create', [
            'usuarios' => $usuarios,
            'lugares' => $lugares,
            'parkings' => $parkings,
            'roles' => $roles
        ]);
    }
    
    /**
     * Almacena un nuevo asalariado
     */
    public function store(Request $request)
    {
        // Verificar que el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        // Validar campos comunes para todos los casos
        $validacionComun = [
            'salario' => 'required|numeric|min:0',
            'id_lugar' => 'required|exists:lugares,id_lugar',
            'parking_id' => 'required|exists:parking,id',
            'tipo_usuario' => 'required|in:existente,nuevo'
        ];
        
        // Añadir validaciones específicas según el tipo de usuario
        if ($request->tipo_usuario === 'existente') {
            $validacionComun['id_usuario'] = 'required|exists:users,id_usuario';
        } else {
            $validacionComun['nombre'] = 'required|string|max:255';
            $validacionComun['email'] = 'required|string|email|max:255|unique:users';
            $validacionComun['password'] = 'required|string|min:8';
            $validacionComun['id_roles'] = 'required|exists:roles,id_roles';
            $validacionComun['dni'] = 'nullable|string|max:20';
            $validacionComun['telefono'] = 'nullable|string|max:20';
        }
        
        $request->validate($validacionComun);
        
        try {
            // Iniciar una transacción para garantizar la integridad
            DB::beginTransaction();
            
            // ID del usuario a asociar con el asalariado
            $idUsuario = null;
            $usuario = null;
            
            // Si es un usuario nuevo, crearlo primero
            if ($request->tipo_usuario === 'nuevo') {
                // Verificar que el rol no sea de cliente
                $rol = Role::findOrFail($request->id_roles);
                if ($rol->nombre_rol === 'cliente') {
                    return redirect()->back()->with('error', 'No se puede crear un asalariado con rol de cliente')->withInput();
                }
                
                // Crear el nuevo usuario
                $usuario = new User();
                $usuario->nombre = $request->nombre;
                $usuario->email = $request->email;
                $usuario->password = Hash::make($request->password);
                $usuario->id_roles = $request->id_roles;
                
                // Campos opcionales
                if ($request->has('dni')) $usuario->dni = $request->dni;
                if ($request->has('telefono')) $usuario->telefono = $request->telefono;
                
                $usuario->save();
                $idUsuario = $usuario->id_usuario;
                
                \Log::info('Nuevo usuario creado con ID: ' . $idUsuario);
            } else {
                // Usuario existente
                $idUsuario = $request->id_usuario;
                
                // Verificar que el usuario no sea un cliente
                $usuario = User::findOrFail($idUsuario);
                if ($usuario->role && $usuario->role->nombre_rol === 'cliente') {
                    return redirect()->back()->with('error', 'No se puede convertir a un cliente en asalariado')->withInput();
                }
            }
            
            // Verificar que el usuario no esté ya asignado como asalariado activo
            $asalariadoActivo = Asalariado::where('id_usuario', $idUsuario)
                ->where('estado', 'alta')
                ->first();
                
            if ($asalariadoActivo) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Este usuario ya está registrado como asalariado activo.')->withInput();
            }
            
            // Verificar si el usuario estuvo dado de baja previamente
            $asalariadoInactivo = Asalariado::where('id_usuario', $idUsuario)
                ->where('estado', 'baja')
                ->first();
                
            if ($asalariadoInactivo) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Este usuario ya fue asalariado y está dado de baja. Puede reactivarlo desde la sección de asalariados inactivos.')->withInput();
            }
            
            // Crear el nuevo asalariado
            $asalariado = new Asalariado();
            $asalariado->id_usuario = $idUsuario;
            $asalariado->salario = $request->salario;
            $asalariado->hiredate = now(); // Fecha de contratación = hoy
            $asalariado->estado = 'alta'; // Estado inicial = alta
            $asalariado->id_lugar = $request->id_lugar;
            $asalariado->parking_id = $request->parking_id;
            $asalariado->save();

            // Guardar el registro
            DB::commit();

            // Mensaje dependiendo de si se creó un usuario nuevo o se usó uno existente
            $mensaje = $request->tipo_usuario === 'nuevo'
                ? 'Asalariado creado correctamente con un nuevo usuario.'
                : 'Asalariado creado correctamente usando un usuario existente.';

            return redirect()->route('admin.asalariados.index')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el asalariado: ' . $e->getMessage())->withInput();
        } // Closes store method's catch block
    } // Closes store method

    /**
     * Obtiene los parkings filtrados por sede para AJAX.
     */
    public function getParkingsBySede(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate(['id_lugar' => 'required|exists:lugares,id_lugar']);
        $idLugar = $request->id_lugar;

        try {
            $parkings = \App\Models\Parking::where('id_lugar', $idLugar)->orderBy('nombre')->get(['id', 'nombre']);
            return response()->json($parkings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener parkings: ' . $e->getMessage()], 500);
        }
    } // Closes getParkingsBySede method

} // Closes AsalariadosController class
