<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Solicitud;

class SolicitudAceptada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitud;
    public $chofer;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
        $this->chofer = $solicitud->chofer->usuario;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('solicitud.' . $this->solicitud->id_cliente);
    }

    public function broadcastWith()
    {
        return [
            'solicitud' => [
                'id' => $this->solicitud->id,
                'estado' => $this->solicitud->estado,
                'precio' => $this->solicitud->precio
            ],
            'chofer' => [
                'id' => $this->chofer->id_usuario,
                'nombre' => $this->chofer->nombre,
                'latitud' => $this->solicitud->chofer->latitud,
                'longitud' => $this->solicitud->chofer->longitud
            ]
        ];
    }

    /**
     * Determina si el evento debe ser encolado.
     */
    public function broadcastQueue()
    {
        return 'sync';
    }
} 