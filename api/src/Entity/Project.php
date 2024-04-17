<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class Project
{
    private string $id;
    private string $name;
    private string $description;

    /**
     * @Assert\Url
     * (
     *    message = "The URL '{{ value }}' is not valid.",
     *    protocols = {"http", "https"}
     * )
     */
    private string $link;
    private User $owner;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(string $name, User $owner)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->name = $name;
        $this->description = '';
        $this->link = '';
        $this->owner = $owner;
        $this->createdAt = new \DateTime();
        $this->markAsUpdated();
        $owner->addProject($this);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner->getId() === $user->getId();
    }

    public function equals(Project $project): bool
    {
        return $this->id === $project->getId();
    }
}