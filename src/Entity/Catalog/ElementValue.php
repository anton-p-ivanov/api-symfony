<?php

namespace App\Entity\Catalog;

use App\Entity\Field\Field;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="catalogs_elements_values")
 * @ORM\Entity()
 */
class ElementValue implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Element", inversedBy="values")
     * @ORM\JoinColumn(name="element_uuid", referencedColumnName="uuid", onDelete="CASCADE")
     */
    private $element;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Field\Field")
     * @ORM\JoinColumn(name="field_uuid", referencedColumnName="uuid", onDelete="CASCADE")
     */
    private $field;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @return Element|null
     */
    public function getElement(): ?Element
    {
        return $this->element;
    }

    /**
     * @return Field|null
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return array|string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}