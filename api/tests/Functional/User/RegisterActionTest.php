<?php

namespace App\Tests\Functional\User;

use Symfony\Component\HttpFoundation\Response;

class RegisterActionTest extends UserTestBase
{
    public function testRegister(): void
    {
        $payload = [
            'name' => 'Stewie',
            'email' => 'stewie@api.com',
            'password' => '123456',
        ];

        self::$client->request('POST', \sprintf('%s/register', $this->endpoint), [], [], [], \json_encode($payload));

        $response = self::$client->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($payload['email'], $responseData['email']);
    }

    public function testRegisterWithMissingParameters(): void
    {
        $payload = [
            'name' => 'Stewie',
            'password' => '123456',
        ];

        self::$client->request('POST', \sprintf('%s/register', $this->endpoint), [], [], [], \json_encode($payload));

        $response = self::$client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRegisterWithInvalidPassword(): void
    {
        $payload = [
            'name' => 'Stewie',
            'email' => 'stewie@api.com',
            'password' => '1',
        ];

        self::$client->request('POST', \sprintf('%s/register', $this->endpoint), [], [], [], \json_encode($payload));

        $response = self::$client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}