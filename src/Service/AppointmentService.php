<?php

namespace App\Service;


class AppointmentService
{

    public function getAvailabilities(int $page): array
    {
        $daysPerPage = 5;

        $startDate = (new \DateTime())->modify('+' . (($page - 1) * $daysPerPage) . ' days');
        $availabilities = [];

        for ($i = 0; $i < $daysPerPage; $i++) {
            $date = clone $startDate;
            $date->modify("+{$i} days");

            $slots = $this->generateRandomSlots();

            $availabilities[] = [
                'date' => $date->format('Y-m-d'),
                'slots' => $slots
            ];
        }
        return $availabilities;
    }

    public function generateRandomSlots(): array
    {
        $possibleSlots = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00'
        ];

        shuffle($possibleSlots);
        $count = random_int(0, 6);
        return array_slice($possibleSlots, 0, $count);
    }
}
