<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activo;
use App\Models\Pasivo;
use App\Models\Asalariado;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BalanceController extends Controller
{
    /**
     * Constructor con middleware para asegurar que solo los administradores financieros accedan
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->id_roles !== 5) {
                return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
            }
            return $next($request);
        });
    }

    /**
     * Muestra la página principal de balance
     */
    public function index(Request $request)
    {
        // Obtener la sede del administrador financiero actual
        $adminFinanciero = Auth::user();
        $adminAsalariado = $adminFinanciero->asalariado;
        
        if (!$adminAsalariado || !$adminAsalariado->parking) {
            return redirect('/')->with('error', 'No tienes una sede asignada. Contacta con el administrador del sistema.');
        }
        
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings asociados a esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // Periodos disponibles
        $periodos = [
            'mes' => 'Mensual',
            'trimestre' => 'Trimestral',
            'año' => 'Anual'
        ];
        
        // Determinar el periodo seleccionado (por defecto mensual)
        $periodoSeleccionado = $request->input('periodo', 'mes');
        
        // Determinar la fecha de inicio y fin basada en el periodo
        $fechaFin = Carbon::now();
        $fechaInicio = null;
        
        switch ($periodoSeleccionado) {
            case 'mes':
                $fechaInicio = Carbon::now()->startOfMonth();
                break;
            case 'trimestre':
                $fechaInicio = Carbon::now()->startOfQuarter();
                break;
            case 'año':
                $fechaInicio = Carbon::now()->startOfYear();
                break;
            default:
                $fechaInicio = Carbon::now()->startOfMonth();
        }
        
        // Obtener el balance inicial (al inicio del periodo)
        $activosIniciales = Activo::where('id_lugar', $sedeId)
            ->where('fecha_registro', '<', $fechaInicio)
            ->sum('valor');
            
        $pasivosIniciales = Pasivo::where('id_lugar', $sedeId)
            ->where('fecha_registro', '<', $fechaInicio)
            ->sum('valor');
            
        $balanceInicial = $activosIniciales - $pasivosIniciales;
        
        // Obtener el balance final (hasta la fecha actual)
        $activosActuales = Activo::where('id_lugar', $sedeId)
            ->where('fecha_registro', '<=', $fechaFin)
            ->sum('valor');
            
        $pasivosActuales = Pasivo::where('id_lugar', $sedeId)
            ->where('fecha_registro', '<=', $fechaFin)
            ->sum('valor');
            
        $balanceFinal = $activosActuales - $pasivosActuales;
        
        // Obtener los activos y pasivos en el periodo seleccionado
        $activosPeriodo = Activo::where('id_lugar', $sedeId)
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->orderBy('categoria')
            ->orderBy('fecha_registro', 'desc')
            ->get();
            
        $pasivosPeriodo = Pasivo::where('id_lugar', $sedeId)
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->orderBy('categoria')
            ->orderBy('fecha_registro', 'desc')
            ->get();
        
        // Calcular totales por categoría de activos
        $totalActivosPorCategoria = $activosPeriodo->groupBy('categoria')
            ->map(function ($items) {
                return $items->sum('valor');
            });
            
        $totalPasivosPorCategoria = $pasivosPeriodo->groupBy('categoria')
            ->map(function ($items) {
                return $items->sum('valor');
            });
        
        // Calcular total de activos y pasivos en el periodo
        $totalActivosPeriodo = $activosPeriodo->sum('valor');
        $totalPasivosPeriodo = $pasivosPeriodo->sum('valor');
        $balancePeriodo = $totalActivosPeriodo - $totalPasivosPeriodo;
        
        // Obtener ingresos por reservas en el periodo
        $ingresosPorReservas = DB::table('reservas')
            ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
            ->join('vehiculos', 'vehiculos_reservas.id_vehiculos', '=', 'vehiculos.id_vehiculos')
            ->whereIn('vehiculos.parking_id', $parkingsIds)
            ->where('reservas.estado', '=', 'completada')
            ->whereBetween('reservas.fecha_inicio', [$fechaInicio, $fechaFin])
            ->sum('reservas.total_precio');
        
        // Obtener gastos por salarios en el periodo
        $gastosSalarios = Asalariado::whereIn('parking_id', $parkingsIds)->sum('salario');
        
        // Si es trimestral, multiplicar por 3, si es anual por 12
        if ($periodoSeleccionado == 'trimestre') {
            $gastosSalarios *= 3;
        } elseif ($periodoSeleccionado == 'año') {
            $gastosSalarios *= 12;
        }
        
        return view('admin_financiero.balance', [
            'sede' => $sede,
            'periodos' => $periodos,
            'periodoSeleccionado' => $periodoSeleccionado,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'balanceInicial' => $balanceInicial,
            'balanceFinal' => $balanceFinal,
            'activosPeriodo' => $activosPeriodo,
            'pasivosPeriodo' => $pasivosPeriodo,
            'totalActivosPorCategoria' => $totalActivosPorCategoria,
            'totalPasivosPorCategoria' => $totalPasivosPorCategoria,
            'totalActivosPeriodo' => $totalActivosPeriodo,
            'totalPasivosPeriodo' => $totalPasivosPeriodo,
            'balancePeriodo' => $balancePeriodo,
            'ingresosPorReservas' => $ingresosPorReservas,
            'gastosSalarios' => $gastosSalarios
        ]);
    }
    
    /**
     * Muestra el formulario para crear un nuevo activo
     */
    public function createActivo()
    {
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Categorías de activos
        $categorias = [
            'circulante' => 'Activo Circulante',
            'fijo' => 'Activo Fijo',
            'diferido' => 'Activo Diferido',
            'intangible' => 'Activo Intangible'
        ];
        
        return view('admin_financiero.activo_create', [
            'sede' => $sede,
            'categorias' => $categorias
        ]);
    }
    
    /**
     * Almacena un nuevo activo
     */
    public function storeActivo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date'
        ]);
        
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        
        Activo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'valor' => $request->valor,
            'fecha_registro' => $request->fecha_registro,
            'id_lugar' => $sedeId
        ]);
        
        return redirect()->route('admin.financiero.balance')
            ->with('success', 'Activo registrado correctamente');
    }
    
    /**
     * Muestra el formulario para crear un nuevo pasivo
     */
    public function createPasivo()
    {
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Categorías de pasivos
        $categorias = [
            'circulante' => 'Pasivo Circulante',
            'fijo' => 'Pasivo Fijo',
            'largo_plazo' => 'Pasivo a Largo Plazo'
        ];
        
        return view('admin_financiero.pasivo_create', [
            'sede' => $sede,
            'categorias' => $categorias
        ]);
    }
    
    /**
     * Almacena un nuevo pasivo
     */
    public function storePasivo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date',
            'fecha_vencimiento' => 'nullable|date'
        ]);
        
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        
        Pasivo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'valor' => $request->valor,
            'fecha_registro' => $request->fecha_registro,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'id_lugar' => $sedeId
        ]);
        
        return redirect()->route('admin.financiero.balance')
            ->with('success', 'Pasivo registrado correctamente');
    }
    
    /**
     * Obtiene datos para gráficos de balance
     */
    public function getChartData(Request $request)
    {
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        
        // Determinar el periodo seleccionado
        $periodoSeleccionado = $request->input('periodo', 'mes');
        
        // Periodos para la gráfica (últimos 12 meses, 4 trimestres o 3 años dependiendo del periodo)
        $periodos = [];
        $activosPorPeriodo = [];
        $pasivosPorPeriodo = [];
        $balancePorPeriodo = [];
        
        switch ($periodoSeleccionado) {
            case 'mes':
                // Últimos 12 meses
                for ($i = 11; $i >= 0; $i--) {
                    $inicio = Carbon::now()->subMonths($i)->startOfMonth();
                    $fin = Carbon::now()->subMonths($i)->endOfMonth();
                    
                    $periodos[] = $inicio->format('M Y');
                    
                    $activos = Activo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $pasivos = Pasivo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $activosPorPeriodo[] = $activos;
                    $pasivosPorPeriodo[] = $pasivos;
                    $balancePorPeriodo[] = $activos - $pasivos;
                }
                break;
                
            case 'trimestre':
                // Últimos 4 trimestres
                for ($i = 3; $i >= 0; $i--) {
                    $inicio = Carbon::now()->subQuarters($i)->startOfQuarter();
                    $fin = Carbon::now()->subQuarters($i)->endOfQuarter();
                    
                    $periodos[] = 'T' . $inicio->quarter . ' ' . $inicio->year;
                    
                    $activos = Activo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $pasivos = Pasivo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $activosPorPeriodo[] = $activos;
                    $pasivosPorPeriodo[] = $pasivos;
                    $balancePorPeriodo[] = $activos - $pasivos;
                }
                break;
                
            case 'año':
                // Últimos 3 años
                for ($i = 2; $i >= 0; $i--) {
                    $inicio = Carbon::now()->subYears($i)->startOfYear();
                    $fin = Carbon::now()->subYears($i)->endOfYear();
                    
                    $periodos[] = $inicio->year;
                    
                    $activos = Activo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $pasivos = Pasivo::where('id_lugar', $sedeId)
                        ->whereBetween('fecha_registro', [$inicio, $fin])
                        ->sum('valor');
                        
                    $activosPorPeriodo[] = $activos;
                    $pasivosPorPeriodo[] = $pasivos;
                    $balancePorPeriodo[] = $activos - $pasivos;
                }
                break;
        }
        
        return response()->json([
            'periodos' => $periodos,
            'activos' => $activosPorPeriodo,
            'pasivos' => $pasivosPorPeriodo,
            'balance' => $balancePorPeriodo
        ]);
    }
}
