<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Attributes as OA;

final class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'app_auth_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/register',
        summary: 'Créer un nouvel utilisateur',
        description: "Permet de créer un compte utilisateur à partir d'une requête JSON avec email et mot de passe.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'utilisateur@exemple.com',
                        description: "Adresse email de l'utilisateur"
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        example: 'SuperMotDePasse123!',
                        description: "Mot de passe de l'utilisateur"
                    ),
                    new OA\Property(
                        property: 'firstname',
                        type: 'string',
                        example: 'Jeremy',
                        description: "Prénom de l'utilisateur"
                    ),
                    new OA\Property(
                        property: 'lastname',
                        type: 'string',
                        example: 'Poulain',
                        description: "Nom de famille de l'utilisateur"
                    ),
                    new OA\Property(
                        property: 'is_driver',
                        type: 'boolean',
                        example: 'false',
                        description: "Indique si l'utilisateur est un conducteur"
                    ),
                    new OA\Property(
                        property: 'phone',
                        type: 'string',
                        example: '0788996633',
                        description: "Numéro de téléphone de l'utilisateur"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Utilisateur créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User created',
                            description: 'Message de confirmation'
                        )
                    ]
                )
            )
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $form = $this->createForm(RegisterUserForm::class, new User());
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            $errors = (string) $form->getErrors(true, false);
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $form->getData();

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'User created'], Response::HTTP_CREATED, [], ['groups' => 'user:read']);
    }
}
