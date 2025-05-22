<?php

namespace App\Exception\Appointment;

use App\Exception\AppointmentException;

class GarageNotFoundException extends AppointmentException {

    public function __construct(string $garageId)
    {
        parent::__construct("Garage avec l'ID {$garageId} non trouvé");
    }
}

