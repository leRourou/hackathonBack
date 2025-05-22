<?php

namespace App\Exception\Appointment;

use App\Exception\AppointmentException;

class VehiculeNotFoundException extends AppointmentException {

    public function __construct(string $vehiculeId)
    {
        parent::__construct("Véhicule avec l'ID {$vehiculeId} non trouvé");
    }
}

