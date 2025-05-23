<?php

namespace App\Controller;

use App\Entity\Vehicule;
use OpenApi\Attributes as OA;
use App\Form\VehiculeStoreForm;
use App\Repository\UserRepository;
use App\Service\VehiculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Attribute\Model;


#[Route(path: '/api/vehicules')]
final class VehiculeController extends AbstractController
{
    #[Route('/{immatriculation}', name: 'app_vehivule_get_immatriculation', methods: ['GET'])]
    #[OA\Get(
        path: '/api/vehicules/{immatriculation}',
        summary: 'Récupère un véhicule par son immatriculation',
        description: "Retourne les informations d'un véhicule correspondant à une immatriculation donnée (au format AA-123-AA).",
        tags: ['Véhicule'],
        parameters: [
            new OA\PathParameter(
                name: 'immatriculation',
                description: "Immatriculation du véhicule (format attendu : AA-123-AA)",
                required: true,
                schema: new OA\Schema(type: 'string', pattern: '^[A-Z]{2}-\d{3}-[A-Z]{2}$')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Véhicule trouvé',
                content: new OA\JsonContent(
                    ref: new Model(type: Vehicule::class, groups: ['vehicule:read'])
                )
            ),
        ]
    )]
    public function getByImmatriculation(string $immatriculation, UserRepository $userRepository): JsonResponse
    {
        $regex = '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/';

        if (!preg_match($regex, $immatriculation)) {
            return $this->json(['error' => 'Immatriculation invalide'], 400);
        }

        $vehicule = new Vehicule();
        $vehicule->setLicensePlate($immatriculation);
        $vehicule->setBrand('Renault');
        $vehicule->setModel('Clio');
        $vehicule->setMileage(123456);
        $vehicule->setVin('1HGCM82633A123456');
        $vehicule->setRegistrationDate(new \DateTime('2020-01-01'));

        $user = $userRepository->findAll();
        
        $vehicule->setUser($user[0]);

        return $this->json($vehicule, 200, [], [
            'groups' => ['vehicule:read'],
        ]);
    }

    #[Route('', name: 'app_vehicule_store', methods: ['POST'])]
    #[OA\Post(
        path: '/api/vehicules',
        summary: 'Ajoute un véhicule',
        description: "Permet à un utilisateur connecté d'enregistrer un nouveau véhicule.",
        tags: ['Véhicule'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Données du véhicule à enregistrer',
            content: new OA\JsonContent(ref: '#/components/schemas/Vehicule')
        ),
    )]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(VehiculeStoreForm::class, new Vehicule());
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json((string) $form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        /** @var Vehicule */
        $vehicule = $form->getData();

        $vehicule->setUser($this->getUser());

        $entityManager->persist($vehicule);
        $entityManager->flush();

        return $this->json(['message' => 'Vehicule created'], Response::HTTP_CREATED);
    }

    #[Route('', name: 'app_vehicule_get_by_user', methods: ['GET'])]
    #[OA\Get(
        path: '/api/vehicules',
        summary: "Liste des véhicules de l'utilisateur",
        description: "Récupère tous les véhicules associés à l'utilisateur actuellement connecté.",
        tags: ['Véhicule'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des véhicule trouvés',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Vehicule::class, groups: ['vehicule:read']))
                )
            ),
        ]
    )]
    public function getVehiculesByUser(VehiculeService $vehiculeService): JsonResponse
    {
        $vehicules = $vehiculeService->getVehiculesByUser($this->getUser());

        return $this->json($vehicules, 200, [], ['groups' => 'vehicule:read']);
    }

    #[Route('/{vehiculeId}/operations', name: 'app_vehicule_get_operations', methods: ['GET'])]
    #[OA\Get(
        path: '/api/vehicules/{vehiculeId}/operations',
        summary: "Prochaines opérations d’un véhicule",
        description: "Récupère les prochaines opérations à effectuer sur le véhicule identifié.",
        tags: ['Véhicule'],
        parameters: [
            new OA\Parameter(
                name: 'vehiculeId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: "Identifiant du véhicule"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des opérations à venir',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'operations',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(
                                        property: 'label',
                                        type: 'string',
                                        example: 'Purge liquide de refroidissement'
                                    ),
                                    new OA\Property(
                                        property: 'next_in_value',
                                        type: 'integer',
                                        example: 55
                                    ),
                                    new OA\Property(
                                        property: 'next_in_unit',
                                        type: 'string',
                                        enum: ['days', 'km'],
                                        example: 'days'
                                    ),
                                    new OA\Property(
                                        property: 'criticality',
                                        type: 'integer',
                                        example: 7
                                    )
                                ]
                            )
                        )
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function getVehiculeNextOperations(VehiculeService $vehiculeService, string $vehiculeId): JsonResponse
    {
        $vehicule = $vehiculeService->getVehiculeById($vehiculeId);
        $nextOperations = $vehiculeService->getNextOperations($vehicule);

        return $this->json([
            "operations" => $nextOperations,
        ], 200, []);
    }
}
