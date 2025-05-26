<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - {{ $numero_factura ?? ('MT-' . str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT)) }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
                :root {
            --color-primario: #9F17BD;
            --color-secundario: #B3B3B3;
            --morado-claro: #c176d6;
            --morado-fondo: #f3e5f5;
        }
        html, body {
            height: 100%;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
            height: 100vh;
            width: 100vw;
            box-sizing: border-box;
        }
        .container {
            min-height: unset;
            height: auto;
            display: block;
            align-items: unset;
            justify-content: unset;
            background: none;
        }
        .factura-container {
            width: 100%;
            max-width: 850px;
            min-height: unset;
            height: auto;
            margin: 30px auto;
            background: white;
            padding: 12px 15px;
            box-shadow: 0 0 12px rgba(0,0,0,0.10);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid var(--color-primario);
            margin-bottom: 12px;
            padding-bottom: 7px;
        }
        .logo {
            max-width: 90px;
            margin-bottom: 2px;
        }
        h1 {
            color: var(--color-primario);
            font-size: 18px;
            margin: 0 0 3px 0;
        }
        h2 {
            color: var(--color-primario);
            font-size: 13px;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .datos-factura {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .datos-empresa, .datos-cliente {
            width: 48%;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
        }
        th, td {
            padding: 4px 5px;
            text-align: left;
            border-bottom: 1px solid var(--color-secundario);
            font-size: 11px;
        }
        th {
            background-color: var(--morado-claro);
            color: white;
            font-size: 11px;
        }
        .total {
            text-align: right;
            font-size: 12px;
            font-weight: bold;
            color: var(--color-primario);
            margin-top: 8px;
        }
        .info-pago {
            margin-top: 10px;
            padding: 7px 8px;
            background-color: var(--morado-fondo);
            border-left: 3px solid var(--color-primario);
            border-radius: 4px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 10px;
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
            html, body {
                width: 210mm;
                height: 297mm;
                min-height: initial;
                max-height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
                font-size: 10px !important;
                zoom: 0.85;
                background: white !important;
            }
            .container {
                width: 210mm;
                height: auto;
                min-height: unset;
                max-height: 297mm;
                align-items: unset;
                justify-content: unset;
                padding: 0 !important;
                margin: 0 !important;
                background: none !important;
            }
            .factura-container {
                width: 100%;
                max-width: 100%;
                height: auto;
                min-height: unset;
                max-height: 100%;
                margin: 0 auto !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 7px 10px !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                overflow: visible;
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
            table, th, td {
                page-break-inside: avoid !important;
            }
            .header h1 {
                font-size: 14px !important;
            }
            h2 {
                font-size: 10px !important;
            }
            th, td {
                font-size: 9px !important;
                padding: 2px 3px !important;
            }
            .total {
                font-size: 10px !important;
            }
            .info-pago, .footer {
                font-size: 9px !important;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="factura-container">
            <div class="header">
                <h1>FACTURA</h1>
                <p><strong>Número:</strong> {{ $numero_factura ?? ('MT-' . str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT)) }}</p>
                <p><strong>Fecha de emisión:</strong> {{ $fecha_emision ?? \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
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
            </div>
            <h2>Detalle del servicio</h2>
            <table>
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th>Fecha</th>
                        <th>Taller</th>
                        <th>Motivo</th>
                        <th>Precio</th>
                        <th>IVA (21%)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->matricula }})</td>
                        <td>{{ $fecha_hora }}</td>
                        <td>{{ $mantenimiento->taller->nombre ?? '-' }}</td>
                        <td>
                            {{ $mantenimiento->motivo_reserva == 'averia' ? 'Avería' : 'Mantenimiento' }}
                            @if($mantenimiento->motivo_reserva == 'averia' && !empty($mantenimiento->motivo_averia))
                                <br><span style="color:#888;font-size:10px;">{{ $mantenimiento->motivo_averia }}</span>
                            @endif
                        </td>
                        <td>€ {{ number_format($precio, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($precio * 0.21, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($precio * 1.21, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            @if(isset($piezas) && count($piezas) > 0)
            <h2 class="mt-3">Piezas utilizadas en la avería</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pieza</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($piezas as $pieza)
                    <tr>
                        <td>{{ $pieza->nombre }}</td>
                        <td>{{ $pieza->pivot->cantidad }}</td>
                        <td>€ {{ number_format($pieza->precio, 2, ',', '.') }}</td>
                        <td>€ {{ number_format($pieza->precio * $pieza->pivot->cantidad, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <div class="total">
                <p>Subtotal servicio: € {{ number_format($precio, 2, ',', '.') }}</p>
                @if(isset($subtotal_piezas) && $subtotal_piezas > 0)
                <p>Subtotal piezas: € {{ number_format($subtotal_piezas, 2, ',', '.') }}</p>
                @endif
                <p><strong>Total (sin IVA): € {{ number_format($precio_total, 2, ',', '.') }}</strong></p>
                <p>IVA (21%): € {{ number_format($precio_total * 0.21, 2, ',', '.') }}</p>
                <p><strong>Total (IVA incluido): € {{ number_format($precio_total * 1.21, 2, ',', '.') }}</strong></p>
            </div>
            <div class="info-pago">
                <h2>Información de pago</h2>
                <p><strong>Estado:</strong> Completado</p>
                <p><strong>Fecha de pago:</strong> {{ $fecha_emision ?? \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
                <p><strong>Referencia:</strong> {{ $numero_factura ?? ('MT-' . str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT)) }}</p>
                <p><strong>Método de pago:</strong> Pago en taller</p>
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