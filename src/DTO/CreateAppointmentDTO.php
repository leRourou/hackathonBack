<?php

namespace App\DTO;

use DateTimeImmutable;

class CreateAppointmentDTO {

    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly string $status,
        public readonly string $notes,
        public readonly string $vehiculeId,
        public readonly string $garageId,
        public readonly array $operationIds = [],
    ) { }
}

