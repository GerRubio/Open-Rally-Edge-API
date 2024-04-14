<?php

namespace App\Service\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\Exception\ORMException;

class CreateProjectService
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function create(User $user, string $name, string $description, string $link): Project
    {
        $project = new Project($name, $user);

        $project->setDescription($description);
        $project->setLink($link);

        try {
            $this->projectRepository->save($project);
        } catch (ORMException) {
            // Excepción personalizada.
        }

        return $project;
    }
}