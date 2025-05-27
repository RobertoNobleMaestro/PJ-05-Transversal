<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - {{ $numero_factura }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --color-primario: #9F17BD;
            --color-secundario: #B3B3B3;
            --morado-claro: #c176d6;
            --morado-fondo: #f3e5f5;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--morado-fondo);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .factura-container {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            padding: 32px 24px;
            box-shadow: 0 0 12px rgba(0,0,0,0.15);
            border-radius: 10px;
            padding-top: 32px;
            padding-bottom: 32px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid var(--color-primario);
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1 {
            color: var(--color-primario);
            font-size: 28px;
        }
        h2 {
            color: var(--color-primario);
            font-size: 20px;
            margin-top: 20px;
        }
        .datos-factura {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .datos-empresa, .datos-cliente {
            width: 48%;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid var(--color-secundario);
        }
        th {
            background-color: var(--morado-claro);
            color: white;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: var(--color-primario);
            margin-top: 20px;
        }
        .info-pago {
            margin-top: 30px;
            padding: 20px;
            background-color: var(--morado-fondo);
            border-left: 5px solid var(--color-primario);
            border-radius: 6px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 40px;
        }
        .actions {
            margin: 20px 0;
            text-align: center;
        }
        .btn-primary {
            background-color: black;
            border-color: black;
        }
        .btn-secondary {
            background-color: var(--color-primario);
            border-color: var(--color-primario);
        }
        .btn-secondary:hover {
    background-color: #6a0080 !important;
    border-color: #6a0080 !important;
    color: #fff !important;
}

.btn-primary:hover {
    background-color: #6a0080 !important;
    border-color: #6a0080 !important;
    color: #fff !important;
}

        .btn-secondary:hover,
        .btn-primary:hover {
            opacity: 0.9;
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
        @media (max-width: 430px) {
            body {
                padding: 0;
                font-size: 13px;
            }
            .factura-container {
                padding: 18px 10px 18px 10px;
                max-width: 100vw;
                border-radius: 0;
                box-shadow: none;
            }
            .header {
                padding-bottom: 4px;
                margin-bottom: 8px;
            }
            .logo {
                max-width: 48px;
                margin-bottom: 2px;
            }
            h1 {
                font-size: 15px;
                margin-bottom: 2px;
            }
            h2 {
                font-size: 13px;
                margin-top: 6px;
                margin-bottom: 2px;
            }
            .datos-factura.row {
                margin-left: 0;
                margin-right: 0;
            }
            .datos-factura .col-12 {
                padding-left: 0;
                padding-right: 0;
            }
            .card {
                border-radius: 8px;
                margin-top: 8px;
                margin-bottom: 8px;
            }
            .card-body {
                padding: 10px 8px;
            }
            .row {
                margin-left: 0;
                margin-right: 0;
            }
            .col-4, .col-6, .col-12, .col-md-6 {
                padding-left: 0;
                padding-right: 0;
            }
            .total {
                font-size: 13px;
                margin-top: 6px;
            }
            .info-pago {
                padding: 6px 2px;
                font-size: 12px;
            }
            .footer {
                font-size: 10px;
                margin-top: 10px;
            }
            .actions {
                margin: 6px 0;
            }
            .btn {
                font-size: 12px;
                padding: 5px 8px;
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
        <br>
        <br>
        <div class="factura-container">
            <div class="header">
                <img src="{{ asset('img/logo.png') }}" alt="Logo de la empresa" class="logo">
                <h1>FACTURA</h1>
                <p><strong>Número:</strong> {{ $numero_factura }}</p>
                <p><strong>Fecha de emisión:</strong> {{ $fecha_emision }}</p>
            </div>

            <div class="datos-factura row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: var(--morado-fondo);">
                        <div class="card-body p-3">
                            <h2 class="mb-2" style="font-size: 1.1rem; color: var(--color-primario);"><i class="fas fa-building mr-2"></i> Empresa</h2>
                            <p class="mb-1"><strong>CarFlow S.L.</strong></p>
                            <p class="mb-1">CIF: B12345678</p>
                            <p class="mb-1">Calle Principal, 123</p>
                            <p class="mb-1">28001 Madrid, España</p>
                            <p class="mb-1">Email: info@carflow.com</p>
                            <p class="mb-0">Teléfono: +34 91 123 45 67</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: var(--morado-fondo);">
                        <div class="card-body p-3">
                            <h2 class="mb-2" style="font-size: 1.1rem; color: var(--color-primario);"><i class="fas fa-user mr-2"></i> Cliente</h2>
                            <p class="mb-1"><strong>{{ $reserva->usuario->nombre }}</strong></p>
                            <p class="mb-1">DNI/NIF: {{ $reserva->usuario->dni }}</p>
                            <p class="mb-1">{{ $reserva->usuario->direccion }}</p>
                            <p class="mb-1">Email: {{ $reserva->usuario->email }}</p>
                            @if($reserva->usuario->telefono)
                            <p class="mb-0">Teléfono: {{ $reserva->usuario->telefono }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach($reserva->vehiculosReservas as $vr)
                @php
                    $fecha_ini = \Carbon\Carbon::parse($vr->fecha_ini);
                    $fecha_fin = \Carbon\Carbon::parse($vr->fecha_final);
                    $dias = $fecha_ini->diffInDays($fecha_fin) + 1;
                    $importe = $vr->vehiculo->precio_dia * $dias;
                @endphp
                <div class="col-12 mb-3">
                    <div class="card shadow-sm border-0 h-100" style="background: var(--morado-fondo);">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-car fa-lg mr-2" style="color: var(--color-primario);"></i>
                                <h5 class="mb-0" style="color: var(--color-primario); font-size: 1.1rem;">{{ $vr->vehiculo->marca }} {{ $vr->vehiculo->modelo }}</h5>
                            </div>
                            <div class="row">
                                <div class="col-6 small"><strong>Período:</strong><br>{{ $fecha_ini->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</div>
                                <div class="col-6 small"><strong>Lugar:</strong><br>{{ $reserva->lugar->nombre ?? 'No especificado' }}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4 small"><strong>Precio/día</strong><br>€ {{ number_format($vr->vehiculo->precio_dia, 2, ',', '.') }}</div>
                                <div class="col-4 small"><strong>Días</strong><br>{{ $dias }}</div>
                                <div class="col-4 small"><strong>Importe</strong><br>€ {{ number_format($importe, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-12 mb-3">
                <div class="card shadow-sm border-0 h-100" style="background: var(--morado-fondo);">
                    <div class="card-body p-3">
                        <h2>Información de pago</h2>
                        <br>
                        <p><strong>Estado:</strong> {{ ucfirst($reserva->estado) }}</p>
                        <p><strong>Fecha de pago:</strong> {{ $fecha_emision }}</p>
                        <p><strong>Método de pago:</strong> Tarjeta de crédito</p>
                    </div>
                </div>
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
