<?php

namespace App\Service\Password;

use App\Entity\User;
use App\Exception\Password\PasswordException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EncoderService
{
    private const MINIMUM_LENGTH = 6;

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function generateEncodedPassword(UserInterface $user, string $password): string
    {
        if (self::MINIMUM_LENGTH > \strlen($password)) {
            throw PasswordException::invalidLength();
        }

        return $this->userPasswordHasher->hashPassword($user, $password);
    }

    public function isValidPassword(User $user, string $oldPassword): bool
    {
        return $this->userPasswordHasher->isPasswordValid($user, $oldPassword);
    }
}