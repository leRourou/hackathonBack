<?php

namespace App\Controller;

use App\Entity\Vehicule;
use OpenApi\Attributes as OA;
use App\Form\VehiculeStoreForm;
use App\Service\VehiculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

     
    #[Route('/', name: 'app_vehicule_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(VehiculeStoreForm::class, new Vehicule());
        $form->submit(json_decode($request->getContent(), true));

        if(!$form->isValid()) {
            return $this->json($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        /** @var Vehicule */
        $vehicule = $form->getData();

        $vehicule->setUser($this->getUser());

        $entityManager->persist($vehicule);
        $entityManager->flush();

        return $this->json(['message' => 'Vehicule created'], Response::HTTP_CREATED);
    }

    #[Route('/', name: 'app_vehicule_get_by_user', methods: ['GET'])]
    public function getVehiculeByUser(VehiculeService $vehiculeService): JsonResponse
    {
        $vehicules = $vehiculeService->getVehiculeByUser($this->getUser());

       return $this->json($vehicules, 200, [], ['groups' => 'vehicule:read']);
    }
}
