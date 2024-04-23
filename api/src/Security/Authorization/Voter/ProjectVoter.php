<?php

namespace App\Security\Authorization\Voter;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    public const PROJECT_CREATE = 'PROJECT_CREATE';
    public const PROJECT_READ = 'PROJECT_READ';
    public const PROJECT_UPDATE = 'PROJECT_UPDATE';
    public const PROJECT_DELETE = 'PROJECT_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, $this->supportedAttributes(), true) && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Project) {
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
            self::PROJECT_CREATE => true,
            self::PROJECT_READ, self::PROJECT_UPDATE, self::PROJECT_DELETE => $subject->getOwner()->getId() === $user->getId() || $this->security->isGranted('ROLE_ADMIN'),
            default => false,
        };
    }

    private function supportedAttributes(): array
    {
        return [
            self::PROJECT_CREATE,
            self::PROJECT_READ,
            self::PROJECT_UPDATE,
            self::PROJECT_DELETE,
        ];
    }
}