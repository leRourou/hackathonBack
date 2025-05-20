<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Vehicule;


final class VehiculeController extends AbstractController
{
    #[Route('/vehicules/{immatriculation}', name: 'app_vehivule_get_immatriculation')]
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
