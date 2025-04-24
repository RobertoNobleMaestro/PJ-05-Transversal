<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - {{ $numero_factura }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .datos-factura {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .datos-empresa, .datos-cliente {
            width: 48%;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
        .info-pago {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .factura-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .actions {
            margin: 20px 0;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .factura-container {
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="actions no-print">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir Factura
            </button>
        </div>
        
        <div class="factura-container">
            <div class="header">
                <h1>FACTURA</h1>
                <p><strong>Número:</strong> {{ $numero_factura }}</p>
                <p><strong>Fecha de emisión:</strong> {{ $fecha_emision }}</p>
            </div>
            
            <div class="datos-factura">
                <div class="datos-empresa">
                    <h2>Datos de la empresa</h2>
                    <p><strong>CarFlow S.L.</strong></p>
                    <p>CIF: B12345678</p>
                    <p>Calle Principal, 123</p>
                    <p>28001 Madrid, España</p>
                    <p>Email: info@carflow.com</p>
                    <p>Teléfono: +34 91 123 45 67</p>
                </div>
                
                <div class="datos-cliente">
                    <h2>Datos del cliente</h2>
                    <p><strong>{{ $reserva->usuario->nombre }}</strong></p>
                    <p>DNI/NIF: {{ $reserva->usuario->DNI }}</p>
                    <p>{{ $reserva->usuario->direccion }}</p>
                    <p>Email: {{ $reserva->usuario->email }}</p>
                    @if($reserva->usuario->telefono)
                    <p>Teléfono: {{ $reserva->usuario->telefono }}</p>
                    @endif
                </div>
            </div>
            
            <h2>Detalle de la reserva</h2>
            <table>
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th>Período</th>
                        <th>Lugar</th>
                        <th>Precio por día</th>
                        <th>Días</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reserva->vehiculosReservas as $vr)
                    @php
                        $fecha_ini = \Carbon\Carbon::parse($vr->fecha_ini);
                        $fecha_fin = \Carbon\Carbon::parse($vr->fecha_final);
                        $dias = $fecha_ini->diffInDays($fecha_fin) + 1;
                        $importe = $vr->vehiculo->precio_dia * $dias;
                    @endphp
                    <tr>
                        <td>{{ $vr->vehiculo->marca }} {{ $vr->vehiculo->modelo }}</td>
                        <td>{{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</td>
                        <td>{{ $reserva->lugar->nombre ?? 'No especificado' }}</td>
                        <td>€ {{ number_format($vr->vehiculo->precio_dia, 2, ',', '.') }}</td>
                        <td>{{ $dias }}</td>
                        <td>€ {{ number_format($importe, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="total">
                <p>Total (IVA incluido): € {{ number_format($reserva->total_precio, 2, ',', '.') }}</p>
            </div>
            
            <div class="info-pago">
                <h2>Información de pago</h2>
                <p><strong>Estado:</strong> {{ ucfirst($reserva->estado) }}</p>
                <p><strong>Fecha de pago:</strong> {{ $fecha_emision }}</p>
                <p><strong>Referencia:</strong> {{ $reserva->referencia_pago ?? $numero_factura }}</p>
                <p><strong>Método de pago:</strong> Tarjeta de crédito</p>
            </div>
            
            <div class="footer">
                <p>Gracias por confiar en CarFlow. Este documento es una factura simplificada válida a efectos fiscales.</p>
                <p>Para cualquier consulta relacionada con esta factura, contacte con nuestro servicio de atención al cliente.</p>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>