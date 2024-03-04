<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Service\User\ActivateAccountService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Uid\Uuid;

class ActivateAccountServiceTest extends UserServiceTestBase
{
    private ActivateAccountService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ActivateAccountService($this->userRepository);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testActivateAccount(): void
    {
        $user = new User('user', 'user@email.com');

        $id = Uuid::v4()->toRfc4122();
        $token = \sha1(\uniqid());

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneInactiveByIdAndTokenOrFail')
            ->with($id, $token)
            ->willReturn($user);

        $user = $this->service->activate($id, $token);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->getToken());
        $this->assertTrue($user->isActive());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testForNonExistingUser(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $token = \sha1(\uniqid());

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneInactiveByIdAndTokenOrFail')
            ->with($id, $token)
            ->willThrowException(new UserNotFoundException(\sprintf('User with ID %s and token %s not found', $id, $token)));

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(\sprintf('User with ID %s and token %s not found', $id, $token));

        $this->service->activate($id, $token);
    }
}