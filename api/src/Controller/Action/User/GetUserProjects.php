<?php

namespace App\Controller\Action\User;

use App\Service\User\GetUserProjectsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetUserProjects
{
    private GetUserProjectsService  $getUserProjectsService;

    public function __construct(GetUserProjectsService $getUserProjectsService)
    {
        $this->getUserProjectsService = $getUserProjectsService;
    }

    public function __invoke(string $id): JsonResponse
    {
        return new JsonResponse(
            $this->getUserProjectsService->getUserProjects($id)
        );
    }
}