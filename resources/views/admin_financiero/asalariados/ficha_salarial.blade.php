<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Salarial - {{ $usuario->nombre }}</title>
    <style>
        @page { margin: 15px; }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #9F17BD;
            padding-bottom: 5px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #9F17BD;
            margin-bottom: 3px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .document-info {
            text-align: right;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            background-color: #9F17BD;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-column {
            width: 48%;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .salary-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .salary-details th, .salary-details td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .salary-details th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #e6d9f2; /* Light purple background */
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 10px;
        }
        .signature {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CARFLOW</div>
        <div class="subtitle">Administración Financiera - Ficha Salarial</div>
    </div>
    
    <div class="document-info" style="margin-top: -10px;">
        <p style="margin: 2px 0;"><strong>Nº Ficha:</strong> {{ $numero_ficha }}</p>
        <p style="margin: 2px 0;"><strong>Fecha de emisión:</strong> {{ $fecha_emision }}</p>
    </div>
    
    <div class="section" style="margin-bottom: 8px;">
        <div class="section-title">Información del Empleado</div>
        <table width="100%" cellpadding="2" cellspacing="0" style="font-size: 11px;">
            <tr>
                <td width="15%"><strong>Nombre:</strong></td>
                <td width="35%">{{ $usuario->nombre }}</td>
                <td width="15%"><strong>Rol:</strong></td>
                <td width="35%">
                    @if($usuario->role->nombre == 'admin_financiero') 
                        Admin Financiero
                    @else 
                        {{ ucfirst($usuario->role->nombre) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>DNI:</strong></td>
                <td>{{ $usuario->dni }}</td>
                <td><strong>Parking:</strong></td>
                <td>{{ $parking->nombre }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $usuario->email }}</td>
                <td><strong>Sede:</strong></td>
                <td>{{ $sede->nombre }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong></td>
                <td>{{ $usuario->telefono ?: 'No disponible' }}</td>
                <td><strong>Antigüedad:</strong></td>
                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="section" style="margin-bottom: 8px;">
        <div class="section-title">Detalle Salarial</div>
        <table width="100%" cellpadding="2" cellspacing="0" style="font-size: 11px; margin-bottom: 5px;">
            <tr>
                <td width="25%"><strong>Salario base:</strong></td>
                <td width="25%">{{ number_format($asalariado->salario, 2, ',', '.') }} €</td>
                <td width="25%"><strong>Día de cobro:</strong></td>
                <td width="25%">Día {{ $asalariado->dia_cobro }} de cada mes</td>
            </tr>
        </table>
        
        <table class="salary-details">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th style="text-align: right">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salario base mensual</td>
                    <td style="text-align: right">{{ number_format($asalariado->salario, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td>Salario anual (12 meses)</td>
                    <td style="text-align: right">{{ number_format($asalariado->salario * 12, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td>Pagas extra (2)</td>
                    <td style="text-align: right">{{ number_format($asalariado->salario * 2, 2, ',', '.') }} €</td>
                </tr>
                <tr class="total-row">
                    <td>Total anual bruto</td>
                    <td style="text-align: right">{{ number_format($asalariado->salario * 14, 2, ',', '.') }} €</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="signatures">
        <div class="signature">
            <div class="signature-line">Firma del Empleado</div>
        </div>
        <div class="signature">
            <div class="signature-line">Firma del Administrador Financiero</div>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 2px 0; font-size: 9px;">Este documento es una ficha informativa y no constituye un contrato laboral. Los importes son brutos antes de impuestos.</p>
        <p style="margin: 2px 0; font-size: 9px;">© {{ date('Y') }} Carflow - Administración Financiera</p>
    </div>
</body>
</html>
