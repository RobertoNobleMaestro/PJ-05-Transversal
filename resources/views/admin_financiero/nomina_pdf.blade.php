<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nómina - {{ $asalariado->usuario->nombre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
            position: relative;
        }
        
        @if(isset($esPreview) && $esPreview)
        /* Estilo para la marca de agua de vista previa */
        body::after {
            content: "VISTA PREVIA";
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: -1;
            font-size: 100px;
            color: rgba(208, 208, 208, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-45deg);
            pointer-events: none;
        }
        @endif
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #6c4dd1;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #6c4dd1;
            margin-bottom: 5px;
        }
        .title {
            font-size: 20px;
            margin-bottom: 5px;
            color: #555;
        }
        .subtitle {
            font-size: 14px;
            color: #777;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            color: #6c4dd1;
        }
        .row {
            display: block;
            margin-bottom: 5px;
        }
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .label {
            float: left;
            width: 50%;
            font-weight: bold;
        }
        .value {
            float: right;
            width: 50%;
            text-align: right;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .summary .row {
            margin-bottom: 8px;
        }
        .net-salary {
            font-size: 16px;
            font-weight: bold;
            color: #6c4dd1;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
        .signature {
            margin-top: 40px;
            padding-top: 20px;
        }
        .signature-line {
            border-top: 1px solid #999;
            width: 70%;
            margin: 0 auto;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CARFLOW</div>
        <div class="title">NÓMINA MENSUAL</div>
        <div class="subtitle">Periodo: {{ $periodo }}</div>
        @if(isset($esPreview) && $esPreview)
        <div style="margin-top: 10px; padding: 5px; background-color: #ffe0e0; border: 1px solid #ff9999; color: #cc0000; text-align: center; font-weight: bold;">
            VISTA PREVIA - Esta nómina aún no ha sido generada oficialmente
        </div>
        @endif
    </div>
    
    <div class="section">
        <div class="section-title">DATOS DE LA EMPRESA</div>
        <div class="row">
            <div class="label">Empresa:</div>
            <div class="value">{{ $empresa }}</div>
        </div>
        <div class="row">
            <div class="label">CIF:</div>
            <div class="value">{{ $cifEmpresa }}</div>
        </div>
        <div class="row">
            <div class="label">Dirección:</div>
            <div class="value">{{ $direccionEmpresa }}</div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">DATOS DEL EMPLEADO</div>
        <div class="row">
            <div class="label">Nombre:</div>
            <div class="value">{{ $asalariado->usuario ? $asalariado->usuario->nombre : 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="label">Email:</div>
            <div class="value">{{ $asalariado->usuario ? $asalariado->usuario->email : 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="label">Posición:</div>
            <div class="value">{{ $asalariado->usuario && $asalariado->usuario->role ? $asalariado->usuario->role->nombre_rol : 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="label">Sede:</div>
            <div class="value">{{ $asalariado->sede ? $asalariado->sede->nombre : 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="label">Fecha de Contratación:</div>
            <div class="value">
                @if($asalariado->hiredate)
                    @if(is_string($asalariado->hiredate))
                        {{ \Carbon\Carbon::parse($asalariado->hiredate)->format('d/m/Y') }}
                    @else
                        {{ $asalariado->hiredate->format('d/m/Y') }}
                    @endif
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">CONCEPTO SALARIAL</div>
        <div class="row">
            <div class="label">Salario Base:</div>
            <div class="value">{{ number_format($salarioBruto, 2, ',', '.') }} €</div>
        </div>
        <div class="row">
            <div class="label">Complementos:</div>
            <div class="value">0,00 €</div>
        </div>
        <div class="row">
            <div class="label">Total Devengado:</div>
            <div class="value">{{ number_format($salarioBruto, 2, ',', '.') }} €</div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">DEDUCCIONES</div>
        <div class="row">
            <div class="label">IRPF (15%):</div>
            <div class="value">{{ number_format($impuestoRenta, 2, ',', '.') }} €</div>
        </div>
        <div class="row">
            <div class="label">Seguridad Social (6.5%):</div>
            <div class="value">{{ number_format($seguridadSocial, 2, ',', '.') }} €</div>
        </div>
        <div class="row">
            <div class="label">Total Deducciones:</div>
            <div class="value">{{ number_format($impuestoRenta + $seguridadSocial, 2, ',', '.') }} €</div>
        </div>
    </div>
    
    <div class="summary">
        <div class="row">
            <div class="label">TOTAL DEVENGADO:</div>
            <div class="value">{{ number_format($salarioBruto, 2, ',', '.') }} €</div>
        </div>
        <div class="row">
            <div class="label">TOTAL DEDUCCIONES:</div>
            <div class="value">{{ number_format($impuestoRenta + $seguridadSocial, 2, ',', '.') }} €</div>
        </div>
        <div class="row net-salary">
            <div class="label">LÍQUIDO A PERCIBIR:</div>
            <div class="value">{{ number_format($salarioNeto, 2, ',', '.') }} €</div>
        </div>
    </div>
    
    <div class="signature">
        <div class="signature-line">Firma del empleado</div>
    </div>
    
    <div class="footer">
        Este documento es una nómina simplificada con fines educativos. Fecha de emisión: {{ $fecha }}
    </div>
</body>
</html>
