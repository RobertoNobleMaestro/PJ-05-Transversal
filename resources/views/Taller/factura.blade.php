<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Mantenimiento</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #222; background: #fff; }
        .factura-box { max-width: 700px; margin: 10px auto; padding: 16px 18px; border-radius: 8px; border: 1px solid #e0e0e0; background: #faf8ff; box-shadow: 0 3px 12px #e0e0e0; }
        .cabecera { display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #6c3bc8; padding-bottom: 8px; margin-bottom: 10px; }
        .logo { width: 75px; }
        .empresa-info { font-size: 12px; color: #6c3bc8; font-weight: bold; }
        .titulo { font-size: 18px; color: #6c3bc8; margin-bottom: 2px; font-weight: bold; }
        .subtitulo { font-size: 12px; color: #333; margin-bottom: 7px; }
        .datos-taller, .datos-factura, .datos-vehiculo, .datos-cliente { margin-bottom: 10px; font-size: 12px; }
        .datos-taller strong, .datos-factura strong, .datos-vehiculo strong, .datos-cliente strong { color: #6c3bc8; }
        .vehiculo-img { width: 90px; border: 1px solid #eee; margin-bottom: 5px; border-radius: 4px; }
        .tabla-detalle { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 8px; font-size: 12px; }
        .tabla-detalle th, .tabla-detalle td { border: 1px solid #e0e0e0; padding: 5px 7px; text-align: left; }
        .tabla-detalle th { background: #ede7f6; color: #6c3bc8; }
        .tabla-detalle td { background: #fff; }
        .total, .iva, .pago { font-size: 13px; color: #222; text-align: right; }
        .total strong { color: #6c3bc8; font-size: 16px; }
        .iva { color: #888; }
        .firma { margin-top: 20px; text-align: right; color: #6c3bc8; font-size: 12px; }
        .footer { margin-top: 18px; font-size: 10px; color: #aaa; text-align: center; border-top: 1px solid #e0e0e0; padding-top: 5px; }
        .separador { border-bottom: 1px solid #e0e0e0; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="factura-box">
        {{-- CABECERA --}}
        <div class="cabecera">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
            <div class="empresa-info">
                CarFlow<br>
                www.carflow.com<br>
                soporte@carflow.com<br>
                Tel: 900 123 456
            </div>
        </div>
        <div class="titulo">Factura de Mantenimiento</div>
        <div class="subtitulo">Servicio realizado en taller asignado</div>

        {{-- DATOS DEL TALLER --}}
        <div class="datos-taller">
            <strong>Taller:</strong> {{ $mantenimiento->taller->nombre ?? 'N/A' }}<br>
            <strong>Dirección:</strong> {{ $mantenimiento->taller->direccion ?? '-' }}<br>
            <strong>Teléfono:</strong> {{ $mantenimiento->taller->telefono ?? '-' }}
        </div>
        {{-- DATOS DE LA FACTURA --}}
        <div class="datos-factura">
            <strong>Nº Factura:</strong> MT-{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}<br>
            <strong>Fecha de emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
            <strong>Método de pago:</strong> Pago en taller<br>
            <strong>Condiciones:</strong> Pago al contado, IVA incluido
        </div>
        <div class="separador"></div>
        {{-- DATOS DEL VEHÍCULO --}}
        <div class="datos-vehiculo">
            @if($imagen)
                <img src="{{ public_path('storage/vehiculos/'.$imagen) }}" class="vehiculo-img"><br>
            @endif
            <strong>Vehículo:</strong> {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->matricula }})<br>
            <strong>Tipo:</strong> {{ $vehiculo->tipo->nombre ?? '-' }}<br>
            <strong>Fecha y hora acordada:</strong> {{ $fecha_hora }}
        </div>
        <div class="separador"></div>
        {{-- DETALLE DEL MANTENIMIENTO --}}
        <table class="tabla-detalle">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Precio Base</th>
                    <th>IVA (21%)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $iva = $precio * 0.21;
                    $total = $precio + $iva;
                @endphp
                <tr>
                    <td>Mantenimiento programado para {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                    <td>{{ number_format($precio, 2) }} €</td>
                    <td>{{ number_format($iva, 2) }} €</td>
                    <td><strong>{{ number_format($total, 2) }} €</strong></td>
                </tr>
            </tbody>
        </table>
        <div class="total">
            <strong>Total a pagar: {{ number_format($total, 2) }} €</strong>
        </div>
        <div class="iva">
            (Incluye IVA 21%)
        </div>
        <div class="firma">
            Firma y sello del taller<br><br>
            ____________________________
        </div>
        <div class="footer">
            CarFlow &mdash; www.carflow.com &mdash; Esta factura ha sido generada electrónicamente y es válida sin firma física.
        </div>
    </div>
</body>
</html>