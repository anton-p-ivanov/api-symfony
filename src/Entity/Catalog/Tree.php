<?php

namespace App\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="catalogs_tree")
 * @ORM\Entity()
 */
class Tree
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="element_uuid", referencedColumnName="uuid")
     */
    private $element;

    /**
     * @ORM\Column(type="integer")
     */
    private $leftMargin;

    /**
     * @ORM\Column(type="integer")
     */
    private $rightMargin;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="Tree")
     * @ORM\JoinColumn(name="root_uuid", referencedColumnName="uuid")
     */
    private $root;

    /**
     * @ORM\ManyToOne(targetEntity="Tree")
     * @ORM\JoinColumn(name="parent_uuid", referencedColumnName="uuid")
     */
    private $parent;

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return Tree
     */
    public function getRoot(): self
    {
        return $this->root;
    }

    /**
     * @return Tree|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @return Element|null
     */
    public function getElement(): ?Element
    {
        return $this->element;
    }

    /**
     * @return int
     */
    public function getLeftMargin(): int
    {
        return $this->leftMargin;
    }

    /**
     * @return int
     */
    public function getRightMargin(): int
    {
        return $this->rightMargin;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
