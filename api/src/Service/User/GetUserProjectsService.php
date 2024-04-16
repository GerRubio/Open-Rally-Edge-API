<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;

class GetUserProjectsService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserProjects(string $id): ?array
    {
        $user = $this->userRepository->findOneByProjects($id);

        $projects = $user->getProjects();
        $data = [];

        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'link' => $project->getLink(),
                'createdAt' => $project->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $project->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return [
            'userId' => $user->getId(),
            'userName' => $user->getName(),
            'userEmail' => $user->getEmail(),
            'userProjects' => $data
        ];
    }
}