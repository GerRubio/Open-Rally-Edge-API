<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordActionTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testResetPassword(): void
    {
        $peterId = $this->getPeterId();
        $peterId = is_array($peterId) ? $peterId[0] : $peterId;

        $payload = [
            'resetPasswordToken' => '123456',
            'password' => 'new-password',
        ];

        self::$peter->request(
            'PUT',
            \sprintf('%s/%s/reset_password', $this->endpoint, $peterId),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$peter->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($peterId, $responseData['id']);
    }
}