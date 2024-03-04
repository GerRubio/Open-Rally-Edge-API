<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Service\User\ResetPasswordService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ResetPasswordServiceTest extends UserServiceTestBase
{
    private ResetPasswordService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ResetPasswordService($this->userRepository, $this->encoderService);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testResetPassword(): void
    {
        $resetPasswordToken = 'abcde';
        $password = 'new-password';

        $user = new User('user', 'user@api.com');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByIdAndResetPasswordToken')
            ->with($user->getId(), $resetPasswordToken)
            ->willReturn($user);

        $user = $this->service->reset($user->getId(), $resetPasswordToken, $password);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->getResetPasswordToken());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testResetPasswordForNonExistingUser(): void
    {
        $resetPasswordToken = 'abcde';
        $password = 'new-password';

        $user = new User('user', 'user@api.com');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByIdAndResetPasswordToken')
            ->with($user->getId(), $resetPasswordToken)
            ->willThrowException(new UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $this->service->reset($user->getId(), $resetPasswordToken, $password);
    }
}