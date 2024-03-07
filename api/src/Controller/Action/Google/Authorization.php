<?php

namespace App\Controller\Action\Google;

use App\Service\Google\GoogleService;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class Authorization
{
    private GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $code = $request->query->get('code');

        if (!$code) {
            return new JsonResponse(
                ['error' => 'Authorization code not provided.'],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $token = $this->googleService->authorize($code);

            return new JsonResponse(
                ['token' => $token]
            );
        } catch (ORMException $exception) {
            return new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}