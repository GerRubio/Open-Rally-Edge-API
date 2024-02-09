<?php

declare(strict_types=1);

namespace App\Exception\User;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    private const MESSAGE = 'Register with E-Mail %s not found';

    public static function fromEmail(string $email): self
    {
        throw new self(\sprintf(self::MESSAGE, $email));
    }

    public static function fromUserIdAndToken(string $id, string $token): self
    {
        throw new self(\sprintf('Register with ID %s and token %s not found', $id, $token));
    }

    public static function fromUserIdAndResetPasswordToken(string $id, string $resetPasswordToken): self
    {
        throw new self(\sprintf('Register with ID %s and resetPasswordToken %s not found', $id, $resetPasswordToken));
    }

    public static function fromUserId(string $id): self
    {
        throw new self(\sprintf('Register with ID %s not found', $id));
    }
}