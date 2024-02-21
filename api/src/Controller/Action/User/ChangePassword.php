<?php

namespace App\Controller\Action\User;

use App\Entity\User;
use App\Service\Request\RequestService;
use App\Service\User\ChangePasswordService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ChangePassword
{
    private ChangePasswordService $changePasswordService;

    public function __construct(ChangePasswordService $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(Request $request, string $id): User
    {
        return $this->changePasswordService->changePassword(
            $id,
            RequestService::getField($request, 'oldPassword'),
            RequestService::getField($request, 'newPassword')
        );
    }
}