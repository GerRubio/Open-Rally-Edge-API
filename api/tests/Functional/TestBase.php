<?php

namespace App\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    protected static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $peter = null;
    protected static ?KernelBrowser $brian = null;
    protected static ?KernelBrowser $roger = null;

    protected function setUp(): void
    {
        if (null === self::$client) {
            self::$client = static::createClient();

            self::$client->setServerParameters(
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/ld+json',
                ]
            );
        }

        if (null === self::$peter) {
            self::$peter = clone self::$client;

            $this->createAuthenticatedUser(self::$peter, 'peter@api.com');
        }

        if (null === self::$brian) {
            self::$brian = clone self::$client;

            $this->createAuthenticatedUser(self::$brian, 'brian@api.com');
        }

        if (null === self::$roger) {
            self::$roger = clone self::$client;

            $this->createAuthenticatedUser(self::$roger, 'roger@api.com');
        }
    }

    private function createAuthenticatedUser(KernelBrowser &$client, string $email): void
    {
        $user = $this->getContainer()->get('App\Repository\UserRepository')->findOneByEmailOrFail($email);
        $token = $this
            ->getContainer()
            ->get('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface')
            ->create($user);

        $client->setServerParameters(
            [
                'HTTP_Authorization' => \sprintf('Bearer %s', $token),
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );
    }

    protected function getResponseData(Response $response): array
    {
        return \json_decode($response->getContent(), true);
    }

    protected function initDbConnection(): Connection
    {
        return $this->getContainer()->get('doctrine')->getConnection();
    }

    /**
     * @throws Exception
     */
    protected function getPeterId(): array
    {
        return $this->initDbConnection()->executeQuery('SELECT id FROM user WHERE email = "peter@api.com"')->fetchFirstColumn();
    }

    /**
     * @throws Exception
     */
    protected function getBrianId(): array
    {
        return $this->initDbConnection()->executeQuery('SELECT id FROM user WHERE email = "brian@api.com"')->fetchFirstColumn();
    }

    /**
     * @throws Exception
     */
    protected function getRogerId(): array
    {
        return $this->initDbConnection()->executeQuery('SELECT id FROM user WHERE email = "roger@api.com"')->fetchFirstColumn();
    }
}