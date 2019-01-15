<?php

namespace App\Entity;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="workflow")
 * @ORM\Entity()
 */
class Workflow implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="uuid", onDelete="SET NULL")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", cascade={"persist"})
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="uuid", onDelete="SET NULL")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkflowStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="status_uuid", referencedColumnName="uuid", onDelete="SET NULL")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", name="is_deleted", options={"default":false})
     */
    private $isDeleted;

    /**
     * Workflow constructor.
     */
    public function __construct()
    {
        $defaults = [
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
            'isDeleted' => false
        ];

        foreach ($defaults as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setUpdatedAt(\DateTime $dateTime): void
    {
        $this->updatedAt = $dateTime;
    }

    /**
     * @return bool
     */
    protected function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return WorkflowStatus|null
     */
    public function getStatus(): ?WorkflowStatus
    {
        return $this->status;
    }

    /**
     * @param WorkflowStatus $status
     */
    public function setStatus(WorkflowStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return User|null
     */
    public function getCreated(): ?User
    {
        return $this->created;
    }

    /**
     * @param User|null $user
     */
    public function setCreated(?User $user): void
    {
        $this->created = $user;
    }

    /**
     * @return User|null
     */
    public function getUpdated(): ?User
    {
        return $this->updated;
    }

    /**
     * @param User|null $user
     */
    public function setUpdated(?User $user): void
    {
        $this->updated = $user;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted === true;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->getStatus()->getCode() === 'PUBLISHED';
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'createdAt', 'updatedAt'
        ];

        foreach ($serializableFields as $field) {
            $result[$field] = $this->$field;
        }

        $result['isPublished'] = $this->isPublished();
        $result['isDeleted'] = $this->isDeleted();

        return $result;
    }
}
