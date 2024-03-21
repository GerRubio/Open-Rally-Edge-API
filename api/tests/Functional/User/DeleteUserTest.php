<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testDeleteUser(): void
    {
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$peter->request('DELETE', \sprintf('%s/%s', $this->endpoint, $peterId));

            $response = self::$peter->getResponse();

            $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }

    /**
     * @throws Exception
     */
    public function testDeleteAnotherUser(): void
    {
        $peterId = $this->getPeterId();

        if (!empty($peterId)) {
            $peterId = $peterId[0];

            self::$brian->request('DELETE', \sprintf('%s/%s', $this->endpoint, $peterId));

            $response = self::$brian->getResponse();

            $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        } else {
            $this->fail('Unable to find Peter ID.');
        }
    }
}