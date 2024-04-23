<?php

namespace App\Security\Authorization\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_READ = 'USER_READ';
    public const USER_UPDATE = 'USER_UPDATE';
    public const USER_DELETE = 'USER_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, $this->supportedAttributes(), true) && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof User) {
            return false;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::USER_READ, self::USER_UPDATE, self::USER_DELETE => $subject->getId() === $user->getId(),
            default => false,
        };
    }

    private function supportedAttributes(): array
    {
        return [
            self::USER_READ,
            self::USER_UPDATE,
            self::USER_DELETE,
        ];
    }
}