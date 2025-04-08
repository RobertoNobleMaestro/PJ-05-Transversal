<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class VehiculosReservas extends Model
    {
        protected $table = 'vehiculos_reservas'; // nombre exacto de tu tabla en la base de datos

        protected $primaryKey = 'id_vehiculos_reservas';

        public function reserva()
        {
            return $this->belongsTo(Reserva::class, 'id_reservas');
        }

        public function vehiculo()
        {
            return $this->belongsTo(Vehiculo::class, 'id_vehiculos');
        }
    }