<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GarageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GarageController extends AbstractController
{

    #[Route('/garages', name: 'app_garages_get_nearest')]
    public function nearestGarages(Request $request, GarageRepository $garageRepository): JsonResponse
    {
        $lat = (float) $request->query->get('latitude');
        $lng = (float) $request->query->get('longitude');
        $page = max(1, (int) $request->query->get('page', 1));

        $garages = $garageRepository->findNearestGarages($lat, $lng, $page);

        return $this->json([
            'garages' => $garages
        ]);
    }


    #[Route('/garages', name: 'app_garages_get')]
    public function getGarages(GarageRepository $garageRepository): JsonResponse
    {
        $garages = $garageRepository->findAll();

        return $this->json([
            'garages' => $garages
        ]);
    }
}
