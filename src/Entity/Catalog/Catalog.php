<?php

namespace App\Entity\Catalog;

use App\Traits\WorkflowTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="catalogs")
 * @ORM\Entity(repositoryClass="App\Repository\Catalog\CatalogRepository")
 * @ORM\EntityListeners({"App\Listener\WorkflowListener"})
 * @UniqueEntity("code")
 */
class Catalog implements \JsonSerializable
{
    use WorkflowTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTrading;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return null|string
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
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive === true;
    }

    /**
     * @return bool
     */
    public function isTrading(): bool
    {
        return $this->isTrading === true;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'uuid', 'type', 'title', 'description', 'code', 'sort'
        ];

        foreach ($serializableFields as $field) {
            $result[$field] = $this->$field;
        }

        $result['isActive'] = $this->isActive();
        $result['isTrading'] = $this->isTrading();

        return $result;
    }
}
