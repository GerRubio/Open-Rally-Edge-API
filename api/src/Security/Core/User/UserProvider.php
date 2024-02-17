<?php

namespace App\Security\Core\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            return $this->userRepository->findOneByEmailOrFail($identifier);
        } catch (UserNotFoundException) {
            throw new UserNotFoundException(\sprintf('User %s not found', $identifier));
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of %s are not supported', \get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);

        $this->userRepository->save($user);
    }
}