<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class GetUserTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testGetUser(): void
    {
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$peter->request('GET', sprintf('%s/%s', $this->endpoint, $peterId));

            $response = self::$peter->getResponse();
            $responseData = $this->getResponseData($response);

            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals($peterId, $responseData['id']);
            $this->assertEquals('peter@api.com', $responseData['email']);
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }

    /**
     * @throws Exception
     */
    public function testGetAnotherUserData(): void
    {
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$brian->request('GET', \sprintf('%s/%s', $this->endpoint, $peterId));

            $response = self::$brian->getResponse();

            $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }
}