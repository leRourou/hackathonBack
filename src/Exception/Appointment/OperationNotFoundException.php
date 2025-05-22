<?php

namespace App\Exception\Appointment;

use App\Exception\AppointmentException;

class OperationNotFoundException extends AppointmentException {

    public function __construct(string $operationId)
    {
        parent::__construct("Opération avec l'ID {$operationId} non trouvée");
    }
}

