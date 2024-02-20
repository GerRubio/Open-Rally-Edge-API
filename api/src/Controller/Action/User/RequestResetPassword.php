<?php

namespace App\Controller\Action\User;

use App\Service\Request\RequestService;
use App\Service\User\RequestResetPasswordService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RequestResetPassword
{
    private RequestResetPasswordService $resetPasswordService;

    public function __construct(RequestResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->resetPasswordService->send(RequestService::getField($request, 'email'));

        return new JsonResponse(['message' => 'Request reset password E-Mail sent']);
    }
}