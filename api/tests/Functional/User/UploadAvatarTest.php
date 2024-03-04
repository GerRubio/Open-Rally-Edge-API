<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class UploadAvatarTest extends UserTestBase
{
    /**
     * @throws Exception
     */
    public function testUploadAvatar(): void
    {
        $peterId = $this->getPeterId();
        $peterId = is_array($peterId) ? $peterId[0] : $peterId;

        $avatar = new UploadedFile(
            __DIR__.'/../../../fixtures/avatar.jpg',
            'avatar.jpg'
        );

        self::$peter->request(
            'POST',
            \sprintf('%s/%s/avatar', $this->endpoint, $peterId),
            [],
            ['avatar' => $avatar]
        );

        $response = self::$peter->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testUploadAvatarWithWrongInputName(): void
    {
        $peterId = $this->getPeterId();
        $peterId = is_array($peterId) ? $peterId[0] : $peterId;

        $avatar = new UploadedFile(
            __DIR__.'/../../../fixtures/avatar.jpg',
            'avatar.jpg'
        );

        self::$peter->request(
            'POST',
            \sprintf('%s/%s/avatar', $this->endpoint, $peterId),
            [],
            ['non-valid-input' => $avatar]
        );

        $response = self::$peter->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}