<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Servicio - Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f3e5f5; }
        .ticket-container { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 0 10px #ccc; padding: 25px 30px; }
        .ticket-header { text-align: center; margin-bottom: 18px; }
        .ticket-header img { max-width: 70px; margin-bottom: 5px; }
        .ticket-title { color: #9F17BD; font-size: 22px; font-weight: bold; }
        .ticket-info { font-size: 15px; margin-bottom: 10px; }
        .ticket-label { font-weight: bold; color: #9F17BD; }
        .ticket-footer { text-align: center; font-size: 12px; color: #888; margin-top: 18px; }
        .ticket-total { font-size: 18px; color: #9F17BD; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <img src="{{ asset('img/logo.png') }}" alt="Carflow logo">
            <div class="ticket-title">Ticket de Servicio</div>
        </div>
        <div class="ticket-info">
            <span class="ticket-label">Referencia:</span> #{{ $solicitud->id }}
        </div>
        <div class="ticket-info">
            <span class="ticket-label">Cliente:</span> {{ $solicitud->cliente ? $solicitud->cliente->nombre : 'No disponible' }}
        </div>
        <div class="ticket-info">
            <span class="ticket-label">Chofer:</span> {{ $solicitud->chofer ? $solicitud->chofer->usuario->nombre : 'No disponible' }}
        </div>
        <div class="ticket-info">
            <span class="ticket-label">Fecha de pago:</span> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
        <div class="ticket-total">
            Total pagado: {{ number_format($solicitud->precio, 2, ',', '.') }} €
        </div>
        <div class="ticket-footer">
            Gracias por confiar en Carflow.<br>
            Este ticket es válido como justificante de pago.
        </div>
    </div>
</body>
</html> 