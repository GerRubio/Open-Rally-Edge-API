<?php

namespace App\Service\Google;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Http\Google\GoogleClient;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleService extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    private ClientRegistry $clientRegistry;
    private EncoderService $encoderService;
    private GoogleClient $googleClient;
    private JWTTokenManagerInterface $JWTTokenManager;
    private UserRepository $userRepository;


    public function __construct(
        ClientRegistry $clientRegistry,
        EncoderService $encoderService,
        GoogleClient $googleClient,
        JWTTokenManagerInterface $JWTTokenManager,
        UserRepository $userRepository
        )
    {
        $this->clientRegistry = $clientRegistry;
        $this->encoderService = $encoderService;
        $this->googleClient = $googleClient;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function authorize(string $code): string
    {
        try {
            $accessToken = $this->googleClient->getAccessToken('authorization_code', ['code' => $code]);
            $userProfile = $this->googleClient->getUserInfo($accessToken);
        } catch (Exception $exception) {
            throw new BadRequestHttpException(sprintf('Google error. Message: %s', $exception->getMessage()));
        }

        $googleEmail = $userProfile['email'] ?? null;
        $googleName = $userProfile['name'] ?? null;

        if (null === $googleEmail) {
            throw new BadRequestHttpException('Google account without E-Mail.');
        }

        try {
            $user = $this->userRepository->findOneByEmailOrFail($googleEmail);
        } catch (UserNotFoundException) {
            $user = $this->createUser($googleName, $googleEmail);
        }

        return $this->JWTTokenManager->create($user);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function createUser(string $name, string $email): User
    {
        $user = new User($name, $email);

        $user->setPassword($this->encoderService->generateEncodedPassword($user, \sha1(\uniqid())));
        $user->setActive(true);
        $user->setToken(null);

        $this->userRepository->save($user);

        return $user;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'google_oauth';
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                return $client->fetchUserFromToken($accessToken);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        $user = $token->getUser();
        $userToken = $this->JWTTokenManager->create($user);

        return new JsonResponse(
            ['token' => $userToken]
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new JsonResponse(
            $message, Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): ?JsonResponse
    {
        $message = strtr($authException->getMessageKey(), $authException->getMessageData());

        return new JsonResponse(
            $message, Response::HTTP_FORBIDDEN
        );
    }
}