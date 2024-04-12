<?php

namespace App\Exception\Project;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectNotFoundException extends NotFoundHttpException
{
    private const MESSAGE = 'Project with ID %s not found';

    public static function fromId(string $id): self
    {
        throw new self(\sprintf(self::MESSAGE, $id));
    }
}