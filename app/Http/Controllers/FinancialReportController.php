<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\Vehiculo;
use App\Models\Reserva;
use App\Models\Activo;
use App\Models\Pasivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Mostrar la página de gastos e ingresos financieros
     */
    public function gastosIngresos(Request $request)
    {
        // Verificar que el usuario sea admin financiero
        if (!Auth::check() || Auth::user()->id_roles !== 5) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        // Obtener la sede del administrador financiero
        $adminFinanciero = Auth::user();
        $adminAsalariado = $adminFinanciero->asalariado;
        
        if (!$adminAsalariado || !$adminAsalariado->parking) {
            return redirect('/')->with('error', 'No tienes una sede asignada. Contacta con el administrador del sistema.');
        }
        
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings asociados a esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();

        // Obtener datos de ingresos y gastos
        $totalGastosPersonal = Asalariado::whereIn('parking_id', $parkingsIds)->sum('salario');
        
        // Obtener vehículos asociados a estos parkings
        $vehiculosIds = Vehiculo::whereIn('parking_id', $parkingsIds)->pluck('id_vehiculos')->toArray();
        
        // Procesar parámetros de filtro
        $añoActual = $request->input('año', Carbon::now()->year);
        $periodoTipo = $request->input('periodo', 'anual');
        $periodoValor = $request->input('valor', null);
        
        // Base query para ingresos
        $query = DB::table('reservas')
            ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
            ->whereIn('vehiculos_reservas.id_vehiculos', $vehiculosIds)
            ->where('reservas.estado', '=', 'completada')
            ->whereYear('reservas.created_at', $añoActual);
        
        // Aplicar filtros según tipo de período
        switch ($periodoTipo) {
            case 'mensual':
                if ($periodoValor) {
                    $query->whereMonth('reservas.created_at', $periodoValor);
                }
                $agruparPor = 'MONTH(reservas.created_at)';
                $etiquetaFormato = 'd MMMM';
                break;
            case 'trimestral':
                if ($periodoValor) {
                    $mesInicio = ($periodoValor - 1) * 3 + 1;
                    $mesFin = $periodoValor * 3;
                    $query->whereRaw("MONTH(reservas.created_at) BETWEEN $mesInicio AND $mesFin");
                }
                $agruparPor = 'MONTH(reservas.created_at)';
                $etiquetaFormato = 'd MMMM';
                break;
            default: // anual
                $agruparPor = 'MONTH(reservas.created_at)';
                $etiquetaFormato = 'MMMM';
                break;
        }
        
        // Ejecutar la consulta para ingresos
        $ingresosPorPeriodo = $query
            ->select(
                DB::raw("$agruparPor as periodo"),
                DB::raw('SUM(reservas.total_precio) as ingresos')
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->keyBy('periodo');
        
        // Formatear datos para la vista
        $etiquetas = [];
        $ingresos = [];
        $gastos = [];
        $beneficios = [];
        
        // Configurar períodos a mostrar según filtro
        if ($periodoTipo === 'mensual' && $periodoValor) {
            $díaInicio = Carbon::create($añoActual, $periodoValor, 1);
            $díaFin = $díaInicio->copy()->endOfMonth();
            $totalDías = $díaFin->day;
            
            for ($día = 1; $día <= $totalDías; $día++) {
                $fecha = Carbon::create($añoActual, $periodoValor, $día);
                $etiquetas[] = $fecha->locale('es')->isoFormat('D MMM');
                
                // Los ingresos estarían por día en este caso, pero no tenemos ese detalle
                // Repartimos proporcionalmente
                $ingresoTotal = $ingresosPorPeriodo->has($periodoValor) ? $ingresosPorPeriodo[$periodoValor]->ingresos : 0;
                $ingresoDiario = $totalDías > 0 ? $ingresoTotal / $totalDías : 0;
                $ingresos[] = $ingresoDiario;
                
                $gastosDiarios = $totalGastosPersonal / $totalDías;
                $gastos[] = $gastosDiarios;
                
                $beneficios[] = $ingresoDiario - $gastosDiarios;
            }
        } elseif ($periodoTipo === 'trimestral' && $periodoValor) {
            $mesInicio = ($periodoValor - 1) * 3 + 1;
            $mesFin = $periodoValor * 3;
            
            for ($mes = $mesInicio; $mes <= $mesFin; $mes++) {
                $nombreMes = Carbon::create($añoActual, $mes, 1)->locale('es')->isoFormat('MMMM');
                $etiquetas[] = $nombreMes;
                
                $ingresoMes = $ingresosPorPeriodo->has($mes) ? $ingresosPorPeriodo[$mes]->ingresos : 0;
                $ingresos[] = $ingresoMes;
                
                $gastoMes = $totalGastosPersonal; // Gastos fijos mensuales (salarios)
                $gastos[] = $gastoMes;
                
                $beneficios[] = $ingresoMes - $gastoMes;
            }
        } else { // Anual (muestra todos los meses)
            for ($mes = 1; $mes <= 12; $mes++) {
                $nombreMes = Carbon::create($añoActual, $mes, 1)->locale('es')->isoFormat('MMMM');
                $etiquetas[] = $nombreMes;
                
                $ingresoMes = $ingresosPorPeriodo->has($mes) ? $ingresosPorPeriodo[$mes]->ingresos : 0;
                $ingresos[] = $ingresoMes;
                
                $gastoMes = $totalGastosPersonal; // Gastos fijos mensuales (salarios)
                $gastos[] = $gastoMes;
                
                $beneficios[] = $ingresoMes - $gastoMes;
            }
        }
        
        // Calcular totales
        $totalIngresos = array_sum($ingresos);
        $totalGastos = array_sum($gastos);
        $totalBeneficios = $totalIngresos - $totalGastos;
        
        // Estadísticas de rendimiento
        $rentabilidad = $totalGastos > 0 ? ($totalBeneficios / $totalGastos) * 100 : 0;
        
        // Datos para el selector de filtros
        $periodos = [
            'anual' => 'Anual',
            'trimestral' => 'Trimestral',
            'mensual' => 'Mensual'
        ];
        
        $años = [];
        for ($i = Carbon::now()->year - 2; $i <= Carbon::now()->year; $i++) {
            $años[$i] = $i;
        }
        
        $trimestres = [
            1 => '1er Trimestre (Ene-Mar)',
            2 => '2do Trimestre (Abr-Jun)',
            3 => '3er Trimestre (Jul-Sep)',
            4 => '4to Trimestre (Oct-Dic)'
        ];
        
        $mesesOpciones = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return view('financiero.gastos-ingresos', [
            'sede' => $sede,
            'etiquetas' => json_encode($etiquetas),
            'ingresos' => json_encode($ingresos),
            'gastos' => json_encode($gastos),
            'beneficios' => json_encode($beneficios),
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'totalBeneficios' => $totalBeneficios,
            'rentabilidad' => $rentabilidad,
            // Filtros
            'periodoTipo' => $periodoTipo,
            'periodoValor' => $periodoValor,
            'añoSeleccionado' => $añoActual,
            'periodos' => $periodos,
            'años' => $años,
            'trimestres' => $trimestres,
            'meses' => $mesesOpciones
        ]);
    }
    
    /**
     * Mostrar la página de balance financiero (activos y pasivos)
     */
    public function balance(Request $request)
    {
        // Verificar que el usuario sea admin financiero
        if (!Auth::check() || Auth::user()->id_roles !== 5) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        // Obtener la sede del administrador financiero
        $adminFinanciero = Auth::user();
        $adminAsalariado = $adminFinanciero->asalariado;
        
        if (!$adminAsalariado || !$adminAsalariado->parking) {
            return redirect('/')->with('error', 'No tienes una sede asignada. Contacta con el administrador del sistema.');
        }
        
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Procesar parámetros de filtro
        $periodoTipo = $request->input('periodo', 'anual');
        $periodoValor = $request->input('valor', null);
        $añoSeleccionado = $request->input('año', Carbon::now()->year);
        
        // Base query para activos
        $activosQuery = Activo::where('id_lugar', $sedeId);
        $pasivosQuery = Pasivo::where('id_lugar', $sedeId);
        
        // Aplicar filtros según tipo de período
        switch ($periodoTipo) {
            case 'mensual':
                if ($periodoValor) {
                    // Filtrar por mes específico
                    $mesInicio = Carbon::create($añoSeleccionado, $periodoValor, 1)->startOfMonth();
                    $mesFin = Carbon::create($añoSeleccionado, $periodoValor, 1)->endOfMonth();
                    
                    $activosQuery->whereBetween('fecha_registro', [$mesInicio, $mesFin]);
                    $pasivosQuery->whereBetween('fecha_registro', [$mesInicio, $mesFin]);
                }
                break;
                
            case 'trimestral':
                if ($periodoValor) {
                    // Calcular meses del trimestre
                    $mesInicio = (($periodoValor - 1) * 3) + 1;
                    $mesFin = $mesInicio + 2;
                    
                    $fechaInicio = Carbon::create($añoSeleccionado, $mesInicio, 1)->startOfMonth();
                    $fechaFin = Carbon::create($añoSeleccionado, $mesFin, 1)->endOfMonth();
                    
                    $activosQuery->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);
                    $pasivosQuery->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);
                }
                break;
                
            default: // anual
                // Filtrar por año
                $añoInicio = Carbon::create($añoSeleccionado, 1, 1)->startOfYear();
                $añoFin = Carbon::create($añoSeleccionado, 12, 31)->endOfYear();
                
                $activosQuery->whereBetween('fecha_registro', [$añoInicio, $añoFin]);
                $pasivosQuery->whereBetween('fecha_registro', [$añoInicio, $añoFin]);
                break;
        }
        
        // Ejecutar las consultas con los filtros aplicados
        $activos = $activosQuery->orderBy('categoria')->get()->groupBy('categoria');
        $pasivos = $pasivosQuery->orderBy('categoria')->get()->groupBy('categoria');
        
        // Calcular totales
        $totalActivos = $activosQuery->sum('valor');
        $totalPasivos = $pasivosQuery->sum('valor');
        $patrimonioNeto = $totalActivos - $totalPasivos;
        
        // Obtener categorías para gráficos
        $categoriasActivos = [];
        $valoresActivos = [];
        
        foreach($activos as $categoria => $items) {
            $categoriasActivos[] = $categoria;
            $valoresActivos[] = $items->sum('valor');
        }
        
        $categoriasPasivos = [];
        $valoresPasivos = [];
        
        foreach($pasivos as $categoria => $items) {
            $categoriasPasivos[] = $categoria;
            $valoresPasivos[] = $items->sum('valor');
        }
        
        // Calcular ratios financieros
        $ratioSolvencia = $totalPasivos > 0 ? $totalActivos / $totalPasivos : 0;
        $ratioEndeudamiento = $totalActivos > 0 ? ($totalPasivos / $totalActivos) * 100 : 0;
        
        // Datos para el selector de filtros
        $periodos = [
            'anual' => 'Anual',
            'trimestral' => 'Trimestral',
            'mensual' => 'Mensual'
        ];
        
        $años = [];
        for ($i = Carbon::now()->year - 2; $i <= Carbon::now()->year; $i++) {
            $años[$i] = $i;
        }
        
        $trimestres = [
            1 => '1er Trimestre (Ene-Mar)',
            2 => '2do Trimestre (Abr-Jun)',
            3 => '3er Trimestre (Jul-Sep)',
            4 => '4to Trimestre (Oct-Dic)'
        ];
        
        $mesesOpciones = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        // Texto del período para mostrar en la vista
        $periodoTexto = 'Año ' . $añoSeleccionado;
        if ($periodoTipo === 'trimestral' && $periodoValor) {
            $periodoTexto = $trimestres[$periodoValor] . ' ' . $añoSeleccionado;
        } elseif ($periodoTipo === 'mensual' && $periodoValor) {
            $periodoTexto = $mesesOpciones[$periodoValor] . ' ' . $añoSeleccionado;
        }
        
        return view('financiero.balance', [
            'sede' => $sede,
            'activos' => $activos,
            'pasivos' => $pasivos,
            'totalActivos' => $totalActivos,
            'totalPasivos' => $totalPasivos,
            'patrimonioNeto' => $patrimonioNeto,
            'categoriasActivos' => json_encode($categoriasActivos),
            'valoresActivos' => json_encode($valoresActivos),
            'categoriasPasivos' => json_encode($categoriasPasivos),
            'valoresPasivos' => json_encode($valoresPasivos),
            'ratioSolvencia' => $ratioSolvencia,
            'ratioEndeudamiento' => $ratioEndeudamiento,
            // Filtros
            'periodoTipo' => $periodoTipo,
            'periodoValor' => $periodoValor,
            'periodoTexto' => $periodoTexto,
            'añoSeleccionado' => $añoSeleccionado,
            'periodos' => $periodos,
            'años' => $años,
            'trimestres' => $trimestres,
            'meses' => $mesesOpciones
        ]);
    }
    
    /**
     * Mostrar el dashboard financiero
     * COMENTADO - Versión solo con Asalariados
     */
    /*public function dashboard()
    {
        // Verificar que el usuario sea admin financiero
        if (!Auth::check() || Auth::user()->id_roles !== 5) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        // Obtener la sede del administrador financiero
        $adminFinanciero = Auth::user();
        $adminAsalariado = $adminFinanciero->asalariado;
        
        if (!$adminAsalariado || !$adminAsalariado->parking) {
            return redirect('/')->with('error', 'No tienes una sede asignada. Contacta con el administrador del sistema.');
        }
        
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings asociados a esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // SECCIÓN 1: GASTOS DE PERSONAL
        // Estadísticas de asalariados por rol
        $estadisticasPersonal = Asalariado::whereIn('parking_id', $parkingsIds)
            ->join('users', 'asalariados.id_usuario', '=', 'users.id_usuario')
            ->join('roles', 'users.id_roles', '=', 'roles.id_roles')
            ->select('roles.nombre as nombre_rol', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(asalariados.salario) as total_salarios'))
            ->groupBy('roles.nombre')
            ->get();
        
        // Total mensual de salarios
        $totalGastosPersonal = Asalariado::whereIn('parking_id', $parkingsIds)->sum('salario');
        
        // Proyección para los próximos 12 meses
        $proyeccionAnual = $totalGastosPersonal * 12;
        
        // SECCIÓN 2: INGRESOS POR RESERVAS
        // Obtener vehículos asociados a estos parkings
        $vehiculosIds = Vehiculo::whereIn('parking_id', $parkingsIds)->pluck('id_vehiculos')->toArray();
        
        // Estadísticas de reservas por mes (últimos 6 meses)
        $fechaInicio = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d H:i:s');
        
        $ingresosReservas = DB::table('reservas')
            ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
            ->whereIn('vehiculos_reservas.id_vehiculos', $vehiculosIds)
            ->where('reservas.estado', '=', 'completada')
            ->whereDate('reservas.created_at', '>=', $fechaInicio)
            ->select(
                DB::raw('YEAR(reservas.created_at) as año'),
                DB::raw('MONTH(reservas.created_at) as mes'),
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(reservas.total_precio) as ingresos')
            )
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
        
        // Formatear los meses para mostrarlos
        $ingresosReservas->each(function($item) {
            $fecha = Carbon::createFromDate($item->año, $item->mes, 1);
            $item->mes_nombre = $fecha->locale('es')->isoFormat('MMMM');
            $item->mes_año = $fecha->locale('es')->isoFormat('MMMM YYYY');
        });
        
        // Calcular los ingresos totales de los últimos 6 meses
        $totalIngresos = $ingresosReservas->sum('ingresos');
        
        // Proyección de ingresos para los próximos 12 meses
        $ingresoPromedio = $totalIngresos > 0 ? $totalIngresos / min(6, $ingresosReservas->count()) : 0;
        $proyeccionIngresos = $ingresoPromedio * 12;
        
        // SECCIÓN 3: RENTABILIDAD Y KPIs
        $beneficio = $totalIngresos - ($totalGastosPersonal * 6); // 6 meses de gastos vs 6 meses de ingresos
        $margen = $totalIngresos > 0 ? ($beneficio / $totalIngresos) * 100 : 0;
        $roi = $totalGastosPersonal > 0 ? ($beneficio / ($totalGastosPersonal * 6)) * 100 : 0;
        
        // Proyección anual de beneficio
        $proyeccionBeneficio = $proyeccionIngresos - $proyeccionAnual;
        
        // SECCIÓN 4: Días de mayor actividad e ingresos
        $diasMasRentables = DB::table('reservas')
            ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
            ->whereIn('vehiculos_reservas.id_vehiculos', $vehiculosIds)
            ->where('reservas.estado', '=', 'completada')
            ->whereDate('reservas.created_at', '>=', $fechaInicio)
            ->select(
                DB::raw('DAYOFWEEK(reservas.fecha_reserva) as dia_semana'),
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(reservas.total_precio) as ingresos')
            )
            ->groupBy('dia_semana')
            ->orderBy('ingresos', 'desc')
            ->get();
        
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diasMasRentables->each(function($item) use ($diasSemana) {
            $item->nombre_dia = $diasSemana[$item->dia_semana - 1];
        });
        
        // SECCIÓN 5: Calendario de pagos a asalariados
        // Agrupar por día de cobro
        $pagosAsalariados = Asalariado::whereIn('parking_id', $parkingsIds)
            ->select('dia_cobro', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(salario) as total_salarios'))
            ->groupBy('dia_cobro')
            ->orderBy('dia_cobro')
            ->get();
        
        // Calcular el próximo día de pago
        $fechaActual = Carbon::now();
        $diasCobro = [5, 10, 15, 20, 25, 30]; // Días típicos de pago
        
        $proximoPago = null;
        $diasHastaProximoPago = 31;
        
        foreach ($diasCobro as $dia) {
            $fechaPago = Carbon::create($fechaActual->year, $fechaActual->month, $dia);
            if ($fechaPago->isPast()) {
                $fechaPago->addMonth();
            }
            
            $diasRestantes = $fechaActual->diffInDays($fechaPago, false);
            if ($diasRestantes > 0 && $diasRestantes < $diasHastaProximoPago) {
                $diasHastaProximoPago = $diasRestantes;
                $proximoPago = $fechaPago;
            }
        }
        
        return view('financiero.dashboard', [
            'sede' => $sede,
            'estadisticasPersonal' => $estadisticasPersonal,
            'totalGastosPersonal' => $totalGastosPersonal,
            'proyeccionAnual' => $proyeccionAnual,
            'ingresosReservas' => $ingresosReservas,
            'totalIngresos' => $totalIngresos,
            'proyeccionIngresos' => $proyeccionIngresos,
            'beneficio' => $beneficio,
            'margen' => $margen,
            'roi' => $roi,
            'proyeccionBeneficio' => $proyeccionBeneficio,
            'diasMasRentables' => $diasMasRentables,
            'pagosAsalariados' => $pagosAsalariados,
            'proximoPago' => $proximoPago,
            'diasHastaProximoPago' => $diasHastaProximoPago
        ]);
    }
    
    /**
     * Mostrar informe de rentabilidad por vehículo
     * COMENTADO - Versión solo con Asalariados
     */
    /*public function vehiculosRentabilidad()
    {
        // Verificar que el usuario sea admin financiero
        if (!Auth::check() || Auth::user()->id_roles !== 5) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings asociados a esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // Obtener vehículos asociados a estos parkings
        $vehiculos = Vehiculo::whereIn('parking_id', $parkingsIds)
            ->with('tipo', 'parking.lugar')
            ->get();
        
        // Calcular rentabilidad por vehículo
        $fechaInicio = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        
        foreach ($vehiculos as $vehiculo) {
            // Total de reservas
            $reservas = DB::table('reservas')
                ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
                ->where('vehiculos_reservas.id_vehiculos', $vehiculo->id_vehiculos)
                ->where('reservas.estado', '=', 'completada')
                ->whereDate('reservas.created_at', '>=', $fechaInicio)
                ->select('reservas.*', 'vehiculos_reservas.fecha_ini', 'vehiculos_reservas.fecha_final')
                ->get();
            
            $vehiculo->total_reservas = $reservas->count();
            $vehiculo->ingresos_totales = $reservas->sum('total_precio');
            
            // Calcular costes de mantenimiento y amortización (estimado)
            $costoBase = $vehiculo->precio_dia * 30; // Valor base mensual
            $costeMantenimiento = $costoBase * 0.15; // 15% del valor base para mantenimiento
            $amortizacion = $vehiculo->precio / 60; // Amortizar el vehículo en 5 años (60 meses)
            
            $vehiculo->coste_mensual = $costeMantenimiento + $amortizacion;
            $vehiculo->beneficio_mensual = ($vehiculo->ingresos_totales / 6) - $vehiculo->coste_mensual;
            $vehiculo->roi = $vehiculo->coste_mensual > 0 ? ($vehiculo->beneficio_mensual / $vehiculo->coste_mensual) * 100 : 0;
            
            // Clasificar rentabilidad
            if ($vehiculo->roi > 30) {
                $vehiculo->clasificacion = 'Alta rentabilidad';
                $vehiculo->color_clase = 'success';
            } elseif ($vehiculo->roi > 10) {
                $vehiculo->clasificacion = 'Rentabilidad media';
                $vehiculo->color_clase = 'info';
            } elseif ($vehiculo->roi > 0) {
                $vehiculo->clasificacion = 'Baja rentabilidad';
                $vehiculo->color_clase = 'warning';
            } else {
                $vehiculo->clasificacion = 'No rentable';
                $vehiculo->color_clase = 'danger';
            }
            
            // Calcular tasa de ocupación
            $diasTotales = Carbon::now()->diffInDays($fechaInicio);
            $diasReservados = $reservas->sum(function($reserva) {
                $inicio = Carbon::parse($reserva->fecha_ini);
                $fin = Carbon::parse($reserva->fecha_final);
                return $inicio->diffInDays($fin) + 1;
            });
            
            $vehiculo->tasa_ocupacion = $diasTotales > 0 ? min(100, ($diasReservados / $diasTotales) * 100) : 0;
        }
        
        // Ordenar por rentabilidad (ROI)
        $vehiculos = $vehiculos->sortByDesc('roi');
        
        return view('financiero.vehiculos', [
            'sede' => $sede,
            'vehiculos' => $vehiculos,
            'periodo' => 'últimos 6 meses'
        ]);
    }*/
    
    /**
     * Mostrar proyecciones financieras
     * COMENTADO - Versión solo con Asalariados
     */
    /*public function proyecciones()
    {
        // Verificar que el usuario sea admin financiero
        if (!Auth::check() || Auth::user()->id_roles !== 5) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener datos históricos para proyecciones
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        $vehiculosIds = Vehiculo::whereIn('parking_id', $parkingsIds)->pluck('id_vehiculos')->toArray();
        
        // Datos históricos de ingresos mensuales (últimos 12 meses)
        $fechaInicio = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d H:i:s');
        
        $datosHistoricos = DB::table('reservas')
            ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
            ->whereIn('vehiculos_reservas.id_vehiculos', $vehiculosIds)
            ->where('reservas.estado', '=', 'completada')
            ->whereDate('reservas.created_at', '>=', $fechaInicio)
            ->select(
                DB::raw('YEAR(reservas.created_at) as año'),
                DB::raw('MONTH(reservas.created_at) as mes'),
                DB::raw('SUM(reservas.total_precio) as ingresos')
            )
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();
        
        // Formatear los datos para crear proyecciones
        $meses = [];
        $ingresos = [];
        $tendencia = [];
        
        foreach ($datosHistoricos as $dato) {
            $fecha = Carbon::createFromDate($dato->año, $dato->mes, 1);
            $meses[] = $fecha->locale('es')->isoFormat('MMM YY');
            $ingresos[] = $dato->ingresos;
            
            // Calcular tendencia lineal simple
            if (count($ingresos) > 1) {
                $tendencia[] = end($ingresos) * 1.05; // Crecimiento 5%
            } else {
                $tendencia[] = $dato->ingresos;
            }
        }
        
        // Proyectar los próximos 12 meses
        $ultimoIngreso = end($ingresos) ?: 0;
        $tasaCrecimiento = 1.05; // 5% mensual
        
        $mesesFuturos = [];
        $proyeccion = [];
        $fechaFin = Carbon::now()->addMonths(12);
        $fechaActual = Carbon::now();
        
        while ($fechaActual->lt($fechaFin)) {
            $fechaActual->addMonth();
            $mesesFuturos[] = $fechaActual->locale('es')->isoFormat('MMM YY');
            $ultimoIngreso *= $tasaCrecimiento;
            $proyeccion[] = round($ultimoIngreso, 2);
        }
        
        // Calcular proyección de gastos
        $gastosPersonal = Asalariado::whereIn('parking_id', $parkingsIds)->sum('salario');
        $gastosProyeccion = [];
        $tasaInflacion = 1.02; // 2% trimestral
        
        for ($i = 0; $i < count($mesesFuturos); $i++) {
            if ($i % 3 == 0 && $i > 0) {
                $gastosPersonal *= $tasaInflacion;
            }
            $gastosProyeccion[] = round($gastosPersonal, 2);
        }
        
        // Calcular beneficio proyectado
        $beneficioProyeccion = [];
        for ($i = 0; $i < count($proyeccion); $i++) {
            $beneficioProyeccion[] = $proyeccion[$i] - $gastosProyeccion[$i];
        }
        
        // Totales acumulados para el año
        $totalIngresosProyectados = array_sum($proyeccion);
        $totalGastosProyectados = array_sum($gastosProyeccion);
        $totalBeneficioProyectado = $totalIngresosProyectados - $totalGastosProyectados;
        
        // ROI proyectado
        $roiProyectado = $totalGastosProyectados > 0 ? ($totalBeneficioProyectado / $totalGastosProyectados) * 100 : 0;
        
        return view('financiero.proyecciones', [
            'sede' => $sede,
            'mesesHistoricos' => json_encode($meses),
            'ingresosHistoricos' => json_encode($ingresos),
            'tendenciaHistorica' => json_encode($tendencia),
            'mesesFuturos' => json_encode($mesesFuturos),
            'proyeccionIngresos' => json_encode($proyeccion),
            'proyeccionGastos' => json_encode($gastosProyeccion),
            'proyeccionBeneficio' => json_encode($beneficioProyeccion),
            'totalIngresosProyectados' => $totalIngresosProyectados,
            'totalGastosProyectados' => $totalGastosProyectados,
            'totalBeneficioProyectado' => $totalBeneficioProyectado,
            'roiProyectado' => $roiProyectado
        ]);
    }*/
}
