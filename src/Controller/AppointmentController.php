<?php

namespace App\Controller;

use App\DTO\CreateAppointmentDTO;
use App\Form\CreateAppointmentForm;
use App\Service\AppointmentService;
use App\Exception\AppointmentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Dompdf\Dompdf;
use Dompdf\Options;

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
            ),
            new OA\QueryParameter(
                name: 'date',
                description: 'Date spécifique pour récupérer les créneaux disponibles',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2025-05-21')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste paginée des créneaux disponibles',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'availabilities',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-05-21'),
                                    new OA\Property(
                                        property: 'slots',
                                        type: 'array',
                                        items: new OA\Items(type: 'string', example: '14:00')
                                    )
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function getAvailabilities(Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $date = $request->query->get('date');
        $page = max((int) $request->query->get('page', 1), 1);

        $availabilities = [];

        if ($date) {
            $availabilities = $appointmentService->getDateAvailabilities($date);
        } else {
            $availabilities = $appointmentService->getAvailabilities($page);
        }

        return $this->json(['availabilities' => $availabilities]);
    }

    #[Route('', name: 'app_appointment_create_appointment', methods: ['POST'])]
    #[OA\Post(
        path: '/api/appointments',
        summary: 'Créer un rendez-vous',
        description: 'Crée un nouveau rendez-vous avec un véhicule, un garage et une liste d’opérations.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['date', 'status', 'notes', 'vehicule_id', 'garage_id', 'operations'],
                properties: [
                    new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2025-05-22 14:00:00'),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                    new OA\Property(property: 'notes', type: 'string', example: 'Client souhaite une vidange et un contrôle des freins.'),
                    new OA\Property(property: 'vehicule_id', type: 'string', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                    new OA\Property(property: 'garage_id', type: 'string', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                    new OA\Property(
                        property: 'operations',
                        type: 'array',
                        items: new OA\Items(type: 'integer'),
                        example: ['0196f274-2ebf-7e7a-a304-5470b0a52028', '0196f274-2ebf-7e7a-a304-5470b0a52028']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Rendez-vous créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'appointment',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 42),
                                new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2025-05-22 14:00:00'),
                                new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'notes', type: 'string', example: 'Client souhaite une vidange et un contrôle des freins.'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-05-20 10:00:00'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-05-20 10:00:00'),
                                new OA\Property(property: 'vehicule_id', type: 'string', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                                new OA\Property(property: 'garage_id', type: 'integer', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                                new OA\Property(
                                    property: 'operations',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: ['0196f274-2ebf-7e7a-a304-5470b0a52028', '0196f274-2ebf-7e7a-a304-5470b0a52028']
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Données manquantes ou incorrectes'
            )
        ]
    )]
    public function createAppointment(Request $request, AppointmentService $appointmentService): JsonResponse
    {
        $form = $this->createForm(CreateAppointmentForm::class);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json([
                'errors' => $this->getFormErrors($form)
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $formData = $form->getData();
            $dto = new CreateAppointmentDTO(
                date: new \DateTimeImmutable($formData['date']),
                status: $formData['status'],
                notes: $formData['notes'] ?? '',
                vehiculeId: $formData['vehicule_id'],
                garageId: $formData['garage_id'],
                operationIds: $formData['operations'] ?? []
            );

            $appointmentService->createAppointment($dto);

            return $this->json([
                'message' => 'Rendez-vous créé avec succès'
            ], Response::HTTP_CREATED);

        } catch (AppointmentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } 
    }

    #[Route('/user', name: 'app_appointment_get_by_user', methods: ['GET'])]
    #[OA\Post(
        path: '/api/appointments/user',
        summary: 'Récupérer les rendez-vous de l’utilisateur',
        description: 'Retourne tous les rendez-vous associés à l’utilisateur actuellement connecté.',
        responses: [
            new OA\Response(
                response: 201,
                description: 'Rendez-vous créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'appointment',
                            type: 'array',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 42),
                                new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2025-05-22 14:00:00'),
                                new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'notes', type: 'string', example: 'Client souhaite une vidange et un contrôle des freins.'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-05-20 10:00:00'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-05-20 10:00:00'),
                                new OA\Property(property: 'vehicule_id', type: 'string', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                                new OA\Property(property: 'garage_id', type: 'integer', example: '0196f274-2ebf-7e7a-a304-5470b0a52028'),
                                new OA\Property(
                                    property: 'operations',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: ['0196f274-2ebf-7e7a-a304-5470b0a52028', '0196f274-2ebf-7e7a-a304-5470b0a52028']
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getAppointmentByUser(AppointmentService $appointmentService): JsonResponse
    {
        $appointments = $appointmentService->getAppointmentsByUser($this->getUser());

        return $this->json($appointments, 200, context: [
            'groups' => ['appointment:read'],
        ]);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    #[Route('/{appointmentId}/pdf', name: 'app_appointment_pdf_summary', methods: ['GET'])]
    #[OA\Get(
        path: '/appointments/{appointmentId}/pdf',
        summary: 'Génère un résumé PDF du rendez-vous',
        description: 'Retourne un fichier PDF contenant les détails complets d’un rendez-vous, incluant le véhicule, le garage et les opérations associées.',
        operationId: 'getAppointmentPdfSummary',
        tags: ['Rendez-vous'],
        parameters: [
            new OA\Parameter(
                name: 'appointmentId',
                in: 'path',
                required: true,
                description: 'Identifiant UUID du rendez-vous',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Fichier PDF généré avec succès',
                content: new OA\JsonContent(
                    type: 'string',
                    format: 'binary',
                    example: 'Fichier PDF en sortie'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Rendez-vous introuvable'
            )
        ]
    )]
    public function generatePDFSummary(AppointmentService $appointmentService, string $appointmentId): Response
    {
        $appointment = $appointmentService->getAppointmentById($appointmentId);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);

        $html = $this->renderView('appointment/pdf-summary.html.twig', [
            'appointment' => $appointment
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rendezvous.pdf"',
        ]);
    }
}
