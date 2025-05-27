<?php
/**
 * Muestra la página de presupuestos para un vehículo amortizado (VERSIÓN CORREGIDA)
 * 
 * @param Request $request
 * @param int $vehiculo_id ID del vehículo amortizado
 * @return \Illuminate\Http\Response
 */
public function presupuestosAmortizado(Request $request, $vehiculo_id)
{
    // Verificar permisos
    $this->verificarAdminFinanciero();
    
    try {
        // Obtener el vehículo
        $vehiculo = Vehiculo::findOrFail($vehiculo_id);
        
        // Verificar que el vehículo está amortizado
        if (!$vehiculo->estaAmortizado()) {
            return redirect()->route('admin.financiero.balance.activos')
                ->with('error', 'El vehículo no está amortizado.');
        }
        
        // Obtener la sede actual
        $usuario = auth()->user();
        
        // CORRECCIÓN: Comprobar si el usuario tiene lugar asignado
        if (!$usuario->lugar) {
            // Si el usuario no tiene lugar, usar el lugar del vehículo
            $sede = $vehiculo->lugar;
            
            if (!$sede) {
                // Si el vehículo tampoco tiene lugar, redirigir con error
                return redirect()->route('admin.financiero.balance.activos')
                    ->with('error', 'No se pudo determinar la sede. Por favor, contacte con el administrador.');
            }
        } else {
            $sede = $usuario->lugar;
        }
        
        $sedeId = $sede->id_lugar;
        
        // Obtener el mes actual para los filtros
        $anioActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        
        // Obtener los datos de presupuestos para el mes actual
        $fechaInicio = Carbon::createFromDate($anioActual, $mesActual, 1)->startOfMonth();
        $fechaFin = Carbon::createFromDate($anioActual, $mesActual, 1)->endOfMonth();
        
        // Calcular ingresos para el período actual
        $ingresos = Reserva::where('id_lugar', $sedeId)
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completada')
            ->sum('importe_total');
        
        // Calcular gastos para el período actual
        $gastoSalarios = Asalariado::whereHas('parking', function($query) use ($sedeId) {
                $query->where('id_lugar', $sedeId);
            })->sum('salario');
        
        // Calcular el resto de gastos...
        // (código simplificado para obtener los gastos)
        $categorias = [
            'Gastos de Personal - Salarios' => $gastoSalarios,
            // Otros gastos...
        ];
        
        $totalGastos = array_sum($categorias);
        
        // Calcular el balance (beneficio o pérdida)
        $balance = $ingresos - $totalGastos;
        $esPositivo = $balance >= 0;
        
        // Obtener presupuestos existentes para el periodo actual
        $presupuestosActuales = Presupuesto::where('id_lugar', $sedeId)
            ->where('periodo_tipo', 'mensual')
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->get()
            ->keyBy('categoria');
        
        // Calcular el costo de reparación del vehículo
        $costoReparacion = $vehiculo->calcularCostoReparacion();
        
        // Verificar si hay suficiente presupuesto para reparar el vehículo
        $presupuestoSuficiente = $esPositivo && $balance >= $costoReparacion;
        
        // Datos para selección de periodos en el gráfico
        $anios = range(2023, Carbon::now()->year);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        // Pasar los datos a la vista
        return view('admin_financiero.presupuestos', [
            'sede' => $sede,
            'titulo' => 'Presupuesto del Mes Actual',
            'periodoSeleccionado' => 'actual',
            'anioSeleccionado' => $anioActual,
            'mesSeleccionado' => $mesActual,
            'anios' => $anios,
            'meses' => $meses,
            'ingresos' => $ingresos,
            'gastos' => $categorias,
            'totalGastos' => $totalGastos,
            'balance' => abs($balance),
            'esPositivo' => $esPositivo,
            'presupuestosActuales' => $presupuestosActuales,
            'vehiculoAmortizado' => $vehiculo,
            'costoReparacion' => $costoReparacion,
            'presupuestoSuficiente' => $presupuestoSuficiente,
            'esMesActual' => true
        ]);
    } catch (\Exception $e) {
        // Registrar el error para depuración
        \Log::error('Error en presupuestosAmortizado: ' . $e->getMessage());
        
        // Redireccionar con mensaje de error genérico
        return redirect()->route('admin.financiero.balance.activos')
            ->with('error', 'Ocurrió un error al procesar el vehículo amortizado. Por favor, contacte con el administrador.');
    }
}
