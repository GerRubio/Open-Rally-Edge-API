<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ProjectRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Project::class;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $this->saveEntity($project);
    }
}