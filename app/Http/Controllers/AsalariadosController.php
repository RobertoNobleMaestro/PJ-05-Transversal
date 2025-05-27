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
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }
        
        $request->validate([
            'salario' => 'required|numeric|min:0',
            'id_lugar' => 'required|exists:lugares,id_lugar',
            'parking_id' => 'required|exists:parking,id'
        ]);
        
        try {
            // Iniciar una transacción para garantizar la integridad
            DB::beginTransaction();
            
            // Obtener el asalariado a actualizar
            $asalariado = Asalariado::with('usuario.role')->findOrFail($id);
            
            // Actualizar el asalariado específico
            $asalariado->update([
                'id_lugar' => $request->id_lugar,
                'parking_id' => $request->parking_id
            ]);
            
            // Si el asalariado tiene un usuario y rol asociado, actualizar salario de todos con el mismo rol
            if ($asalariado->usuario && $asalariado->usuario->role) {
                $rolId = $asalariado->usuario->role->id;
                
                // Obtener todos los asalariados con el mismo rol
                $asalariadosMismoRol = Asalariado::whereHas('usuario', function($query) use ($rolId) {
                    $query->where('id_roles', $rolId);
                })->get();
                
                // Actualizar el salario de todos los asalariados con el mismo rol
                foreach ($asalariadosMismoRol as $asalariadoRol) {
                    $asalariadoRol->salario = $request->salario;
                    $asalariadoRol->save();
                }
                
                $mensaje = 'Información y salario actualizados para todos los asalariados con el mismo rol.';
            } else {
                // Si no tiene rol, solo actualizar su salario
                $asalariado->salario = $request->salario;
                $asalariado->save();
                $mensaje = 'Información del asalariado actualizada correctamente.';
            }
            
            DB::commit();
            return redirect()->route('admin.asalariados.index')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el detalle de un asalariado
     */
    public function show($id)
    {
        $asalariado = Asalariado::with(['usuario', 'usuario.role', 'sede', 'parking'])->findOrFail($id);
        
        return view('admin_financiero.asalariados.show', [
            'asalariado' => $asalariado
        ]);
    }
    
    /**
     * Genera la nómina en PDF para un asalariado
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
            $salarioBruto = $asalariado->calcularSalarioProporcional();
        } else {
            // Calcular salario proporcional para empleados en proceso de baja
            $salarioBruto = $asalariado->calcularSalarioBaja();
        }
        
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
     * Dar de baja a un asalariado (cambiar estado y calcular días trabajados)
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
        
        // Calcular días trabajados desde su contratación o última alta
        $fechaInicio = $asalariado->hiredate;
        $fechaActual = Carbon::now();
        $diasTrabajados = $fechaInicio->diffInDays($fechaActual) + 1; // +1 para incluir el día actual
        
        // Actualizar estado y días trabajados
        $asalariado->estado = 'baja';
        $asalariado->dias_trabajados = $diasTrabajados;
        $asalariado->save();
        
        return redirect()->back()->with('success', 'Asalariado dado de baja correctamente. Cobrará la parte proporcional de su salario el día 1 del próximo mes.');
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
        
        $asalariado = Asalariado::findOrFail($id);
        
        if ($asalariado->estado == 'alta') {
            return redirect()->back()->with('error', 'Este asalariado ya está dado de alta');
        }
        
        // Actualizar estado, fecha de contratación y reiniciar días trabajados
        $asalariado->estado = 'alta';
        $asalariado->hiredate = Carbon::now();
        $asalariado->dias_trabajados = 0;
        $asalariado->save();
        
        return redirect()->route('admin.asalariados.index')
            ->with('success', 'Asalariado dado de alta correctamente con fecha de contratación actualizada.');
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
        }
    }
}
