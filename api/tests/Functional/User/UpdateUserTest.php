<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testUpdateUser(): void
    {
        $payload = ['name' => 'Peter New'];
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$peter->request(
                'PUT',
                \sprintf('%s/%s', $this->endpoint, $peterId),
                [],
                [],
                [],
                \json_encode($payload)
            );

            $response = self::$peter->getResponse();
            $responseData = $this->getResponseData($response);

            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals($payload['name'], $responseData['name']);
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }

    /**
     * @throws Exception
     */
    public function testUpdateAnotherUser(): void
    {
        $payload = ['name' => 'Peter New'];
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$brian->request(
                'PUT',
                \sprintf('%s/%s', $this->endpoint, $peterId),
                [],
                [],
                [],
                \json_encode($payload)
            );

            $response = self::$brian->getResponse();

            $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }
}