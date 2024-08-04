<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Service\File\FileService;
use App\Service\User\UploadAvatarService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UploadAvatarServiceTest extends UserServiceTestBase
{
    /** @var FileService|MockObject */
    private FileService|MockObject $fileService;

    private UploadAvatarService $service;

    public function setUp(): void
    {
        parent::setUp();

        $mediaPath = 'https://storage.com/';
        $this->fileService = $this->getMockBuilder(FileService::class)->disableOriginalConstructor()->getMock();

        $this->service = new UploadAvatarService($this->userRepository, $this->fileService, $mediaPath);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testUploadAvatar(): void
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $file = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();

        $user = new User('name', 'name@api.com');

        $user->setAvatar('abc.png');

        $this->fileService
            ->expects($this->exactly(1))
            ->method('validateFile')
            ->with($request, FileService::AVATAR_INPUT_NAME)
            ->willReturn($file);

        $this->fileService
            ->expects($this->exactly(1))
            ->method('deleteFile')
            ->with($user->getAvatar());

        $this->fileService
            ->expects($this->exactly(1))
            ->method('uploadFile')
            ->with($file, FileService::AVATAR_INPUT_NAME)
            ->willReturn('aaa.png');

        $response = $this->service->uploadAvatar($request, $user);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals('aaa.png', $response->getAvatar());
    }
}