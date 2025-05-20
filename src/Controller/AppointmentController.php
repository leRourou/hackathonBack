<?php

namespace App\Controller;

use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class AppointmentController extends AbstractController
{
    #[Route('/appointments/avaibilities', name: 'app_appointment_get_availabilities')]
    public function getAvailabilities(Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $page = max((int) $request->query->get('page', 1), 1);
        $availabilities = $appointmentService->getAvailabilities($page);

        return $this->json(['availabilities' => $availabilities]);
    }
}
