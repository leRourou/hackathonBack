<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GarageRepository;
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
                required: true,
                schema: new OA\Schema(type: 'number', format: 'float')
            ),
            new OA\QueryParameter(
                name: 'longitude',
                description: 'Longitude du point de recherche',
                required: true,
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


    #[Route('/', name: 'app_garages_get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/garages',
        summary: 'Récupère tous les garages',
        description: "Retourne la liste complète de tous les garages disponibles en base de données.",
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des garages récupérée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'garages',
                            type: 'array',
                            items: new OA\Items(type: 'object') // Tu peux détailler ici la structure d'un garage si besoin
                        )
                    ]
                )
            )
        ]
    )]
    public function getGarages(GarageRepository $garageRepository): JsonResponse
    {
        $garages = $garageRepository->findAll();

        return $this->json([
            'garages' => $garages
        ]);
    }
}
