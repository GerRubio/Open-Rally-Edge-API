<?php

namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_READ = 'USER_READ';
    public const USER_DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, $this->supportedAttributes(), true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (\in_array($attribute, $this->supportedAttributes(), true)) {
            return true;
        }

        return false;
    }

    private function supportedAttributes(): array
    {
        return [
            self::USER_READ,
            self::USER_DELETE,
        ];
    }
}