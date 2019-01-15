<?php

namespace App\Entity\Storage;

use App\Entity\Role;
use App\Traits\WorkflowTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage")
 * @ORM\Entity()
 */
class Storage implements \JsonSerializable
{
    use WorkflowTrait;
    /**
     * Storage type `file`
     */
    const STORAGE_TYPE_FILE = 'F';
    /**
     * Storage type `directory`
     */
    const STORAGE_TYPE_DIR = 'D';

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=1, options={"fixed" = true})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(
     *     name="storage_roles",
     *     joinColumns={@ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_uuid", referencedColumnName="uuid")}
     * )
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Storage\Version", mappedBy="storage")
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid")
     */
    private $versions;

    /**
     * @var File|null
     */
    private $file;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isDirectory(): bool
    {
        return $this->type === self::STORAGE_TYPE_DIR;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @return Collection|Version[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        $versions = $this->getVersions()->filter(
            function (Version $version) {
                return $version->isActive();
            }
        );

        if (!$versions->isEmpty()) {
            return $versions->first()->getFile();
        }

        return null;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'uuid',
            'type',
            'title',
            'description',
            'workflow',
        ];

        foreach ($serializableFields as $field) {
            $result[$field] = $this->$field;
        }

        $result['file'] = $this->getFile();
        $result['roles'] = $this->getRoles()
            ->map(
                function (Role $role) {
                    return $role->getCode();
                }
            )
            ->toArray();

        return $result;
    }
}
