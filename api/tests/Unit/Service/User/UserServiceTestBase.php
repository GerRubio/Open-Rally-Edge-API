<?php

namespace App\Tests\Unit\Service\User;

use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class UserServiceTestBase extends TestCase
{
    /** @var UserRepository|MockObject */
    protected UserRepository|MockObject $userRepository;

    /** @var EncoderService|MockObject */
    protected MockObject|EncoderService $encoderService;

    /** @var MessageBusInterface|MockObject */
    protected MessageBusInterface|MockObject $messageBus;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $this->encoderService = $this->getMockBuilder(EncoderService::class)->disableOriginalConstructor()->getMock();
        $this->messageBus = $this->getMockBuilder(MessageBusInterface::class)->disableOriginalConstructor()->getMock();
    }
}