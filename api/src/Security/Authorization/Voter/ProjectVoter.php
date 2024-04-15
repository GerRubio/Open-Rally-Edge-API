<?php

namespace App\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    public const PROJECT_READ = 'PROJECT_READ';
    public const PROJECT_DELETE = 'PROJECT_DELETE';

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
            self::PROJECT_READ,
            self::PROJECT_DELETE,
        ];
    }
}