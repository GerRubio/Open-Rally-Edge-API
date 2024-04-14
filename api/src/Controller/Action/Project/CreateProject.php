<?php

namespace App\Controller\Action\Project;

use App\DTO\Project\ProjectDTO;
use App\Entity\Project;
use App\Entity\User;
use App\Service\Project\CreateProjectService;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateProject
{
    private CreateProjectService $createProjectService;

    public function __construct(CreateProjectService $createProjectService)
    {
        $this->createProjectService = $createProjectService;
    }

    public function __invoke(ProjectDTO $data, User $user): Project
    {
        return $this->createProjectService->create(
            $user,
            $data->name,
            $data->description,
            $data->link
        );
    }
}