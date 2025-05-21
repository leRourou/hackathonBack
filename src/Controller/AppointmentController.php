<?php

namespace App\Controller;

use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;

#[Route('/api/appointments', name: 'app_appointment_')]
final class AppointmentController extends AbstractController
{
    #[Route('/avaibilities', name: 'app_appointment_get_availabilities', methods: ['GET'])]
    #[OA\Get(
        path: '/api/appointments/avaibilities',
        summary: 'Récupère les créneaux de rendez-vous disponibles',
        description: 'Retourne les créneaux disponibles paginés pour la prise de rendez-vous.',
        parameters: [
            new OA\QueryParameter(
                name: 'page',
                description: 'Numéro de page pour la pagination des créneaux disponibles',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            )
        ]
    )]
    public function getAvailabilities(Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $page = max((int) $request->query->get('page', 1), 1);
        $availabilities = $appointmentService->getAvailabilities($page);

        return $this->json(['availabilities' => $availabilities]);
    }

    #[Route('', name: 'new_appointment', methods: ['POST'])]
    public function newAppointment(Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $appointment = $appointmentService->createAppointment($data);

        return $this->json(['appointment' => $appointment], 201);
    }
}
