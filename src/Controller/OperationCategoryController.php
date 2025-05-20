<?php

namespace App\Controller;

use App\Service\OperationCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class OperationCategoryController extends AbstractController
{
    #[Route('/operations/category', name: 'app_operation_category_get_all')]
    public function getCategories(OperationCategoryService $operationCategoryService): JsonResponse
    {
        $operations = $operationCategoryService->getAllOperationCategories();
        return $this->json($operations, context: [
            'groups' => ['operation_category:read'],
        ]);
    }
}
