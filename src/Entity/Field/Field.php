<?php

namespace App\Entity\Field;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fields")
 * @ORM\Entity()
 */
class Field
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=32, options={"fixed":true})
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=1, options={"fixed":true})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMultiple;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExpanded;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inList;

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
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
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
    public function getHash(): ?string
    {
        return $this->hash;
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
    public function isMultiple(): bool
    {
        return $this->isMultiple === true;
    }

    /**
     * @return bool
     */
    public function isExpanded(): bool
    {
        return $this->isExpanded === true;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getOptions(): ?string
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    protected function getInList(): bool
    {
        return $this->inList;
    }

    /**
     * @return bool
     */
    protected function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    protected function getIsMultiple(): bool
    {
        return $this->isMultiple;
    }

    /**
     * @return bool
     */
    protected function getIsExpanded(): bool
    {
        return $this->isExpanded;
    }
}
