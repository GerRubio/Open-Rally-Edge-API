<?php

namespace App\Controller\Action\User;

use App\Entity\User;
use App\Service\User\UploadAvatarService;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UploadAvatar
{
    private UploadAvatarService $uploadAvatarService;

    public function __construct(UploadAvatarService $uploadAvatarService)
    {
        $this->uploadAvatarService = $uploadAvatarService;
    }

    /**
     * @throws ORMException
     */
    public function __invoke(Request $request, User $user): User
    {
        return $this->uploadAvatarService->uploadAvatar($request, $user);
    }
}