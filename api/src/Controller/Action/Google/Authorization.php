<?php

namespace App\Controller\Action\Google;

use App\Service\Google\GoogleService;
use App\Service\Request\RequestService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class Authorization
{
    private GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(
            ['token' => $this->googleService->authorize(RequestService::getField($request, 'code'))]
        );
    }
}