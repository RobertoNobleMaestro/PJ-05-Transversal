<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\Vehiculo;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
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
