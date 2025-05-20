<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Vehicule;
use OpenApi\Attributes as OA;

#[Route(path: '/api/vehicules')]
final class VehiculeController extends AbstractController
{
    #[Route('/{immatriculation}', name: 'app_vehivule_get_immatriculation', methods: ['GET'])]
    #[OA\Get(
        path: '/api/vehicules/{immatriculation}',
        summary: 'Récupère un véhicule par son immatriculation',
        description: "Retourne les informations d'un véhicule correspondant à une immatriculation donnée (au format AA-123-AA).",
        parameters: [
            new OA\PathParameter(
                name: 'immatriculation',
                description: "Immatriculation du véhicule (format attendu : AA-123-AA)",
                required: true,
                schema: new OA\Schema(type: 'string', pattern: '^[A-Z]{2}-\d{3}-[A-Z]{2}$')
            )
        ]
    )]

    public function getByImmatriculation(string $immatriculation): JsonResponse
    {
        // TODO : Faire avec un FormType
        $regex = '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/';

        if (!preg_match($regex, $immatriculation)) {
            return $this->json(['error' => 'Immatriculation invalide'], 400);
        }

        $vehicule = new Vehicule();

        $vehicule->setLicensePlate($immatriculation);
        $vehicule->setBrand('Renault');
        $vehicule->setModel(model: 'Clio');
        $vehicule->setMileage(123456);
        $vehicule->setVin('1HGCM82633A123456');
        $vehicule->setRegistrationDate(new \DateTime('2020-01-01'));

        return $this->json($vehicule);
    }
}
