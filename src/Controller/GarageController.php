<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GarageRepository;
use App\Service\GarageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

#[Route(path: '/api/garages')]
final class GarageController extends AbstractController
{
    #[Route('/', name: 'app_garages_get_nearest', methods: ['GET'])]
    #[OA\Get(
        path: '/api/garages',
        summary: 'Récupère les garages les plus proches',
        description: "Retourne une liste de garages triés par distance à partir d'une latitude et longitude données.",
        parameters: [
            new OA\QueryParameter(
                name: 'latitude',
                description: 'Latitude du point de recherche',
                required: false,
                schema: new OA\Schema(type: 'number', format: 'float')
            ),
            new OA\QueryParameter(
                name: 'longitude',
                description: 'Longitude du point de recherche',
                required: false,
                schema: new OA\Schema(type: 'number', format: 'float')
            ),
            new OA\QueryParameter(
                name: 'page',
                description: 'Numéro de page (pagination)',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            )
        ]
    )]
    public function nearestGarages(Request $request, GarageService $garageService): JsonResponse
    {
        $lat = (float) $request->query->get('latitude');
        $lng = (float) $request->query->get('longitude');
        $page = max(1, (int) $request->query->get('page', 1));

        $garages = [];

        if ($lat && $lng) {
            $garages = $garageService->findNearestGarages($lat, $lng, $page);
        } else {
            $garages = $garageService->getAllGarages();
        }

        return $this->json([
            'garages' => $garages
        ]);
    }
}
