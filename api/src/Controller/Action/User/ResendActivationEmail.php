<?php

namespace App\Controller\Action\User;

use App\Service\Request\RequestService;
use App\Service\User\ResendActivationEmailService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ResendActivationEmail
{
    private ResendActivationEmailService $activationEmailService;

    public function __construct(ResendActivationEmailService $activationEmailService)
    {
        $this->activationEmailService = $activationEmailService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->activationEmailService->resend(RequestService::getField($request, 'email'));

        return new JsonResponse(['message' => 'Activation E-Mail sent']);
    }
}