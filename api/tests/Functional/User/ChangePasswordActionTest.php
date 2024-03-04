<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordActionTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testChangePassword(): void
    {
        $peterId = $this->getPeterId();
        $peterId = is_array($peterId) ? $peterId[0] : $peterId;

        $payload = [
            'oldPassword' => 'password',
            'newPassword' => 'new-password',
        ];

        self::$peter->request(
            'PUT',
            \sprintf('%s/%s/change_password', $this->endpoint, $peterId),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$peter->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangePasswordWithInvalidOldPassword(): void
    {
        $peterId = $this->getPeterId();
        $peterId = is_array($peterId) ? $peterId[0] : $peterId;

        $payload = [
            'oldPassword' => 'non-a-good-one',
            'newPassword' => 'new-password',
        ];

        self::$peter->request(
            'PUT',
            \sprintf('%s/%s/change_password', $this->endpoint, $peterId),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$peter->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}