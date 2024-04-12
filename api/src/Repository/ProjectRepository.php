<?php

namespace App\Repository;

use App\Entity\Project;
use App\Exception\Project\ProjectNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ProjectRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Project::class;
    }

    public function findOneByIdOrFail(string $id): Project
    {
        if (null === $group = $this->objectRepository->find($id)) {
            throw ProjectNotFoundException::fromId($id);
        }

        return $group;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $this->saveEntity($project);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Project $project): void
    {
        $this->removeEntity($project);
    }
}