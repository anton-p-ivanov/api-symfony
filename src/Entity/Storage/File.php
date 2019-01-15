<?php

namespace App\Entity\Storage;

use App\Traits\WorkflowTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_files")
 * @ORM\Entity()
 */
class File implements \JsonSerializable
{
    use WorkflowTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=32, options={"fixed": true})
     */
    private $hash;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'uuid', 'name', 'size', 'type', 'hash', 'workflow'
        ];

        foreach ($serializableFields as $field) {
            $result[$field] = $this->$field;
        }

        return $result;
    }
}
