<?php

namespace App\Entity\Catalog;

use App\Traits\WorkflowTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="catalogs_elements")
 * @ORM\Entity(repositoryClass="App\Repository\Catalog\ElementRepository")
 * @ORM\EntityListeners({"App\Listener\WorkflowListener"})
 */
class Element implements \JsonSerializable
{
    use WorkflowTrait;

    const TYPE_ELEMENT = 'E';
    const TYPE_SECTION = 'S';

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $activeFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $activeTo;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalog\Catalog")
     * @ORM\JoinColumn(name="catalog_uuid", referencedColumnName="uuid")
     */
    private $catalog;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Catalog\Tree", mappedBy="element")
     */
    private $nodes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Catalog\ElementValue", indexBy="field.code", mappedBy="element")
     */
    private $values;

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
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive === true && ($this->activeTo === null || $this->activeTo > new \DateTime());
    }

    /**
     * @return bool
     */
    protected function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return \DateTime|null
     */
    public function getActiveFrom(): ?\DateTime
    {
        return $this->activeFrom;
    }

    /**
     * @return \DateTime|null
     */
    public function getActiveTo(): ?\DateTime
    {
        return $this->activeTo;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Catalog|null
     */
    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    /**
     * @return Collection|Tree[]
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    /**
     * @return Collection
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getSections(): array
    {
        $sections = [];

        foreach ($this->getNodes() as $node) {
            $section = $node->getParent()->getElement();
            if ($section) {
                $sections[$section->getCode()] = $section->getTitle();
            }
        }

        return $sections;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'uuid', 'type', 'title', 'description', 'content',
            'code', 'sort', 'activeFrom', 'activeTo',
            'values', 'sections'
        ];

        foreach ($serializableFields as $field) {
            $value = $this->{"get" . ucfirst($field)}();

            if ($value instanceof Collection) {
                if ($field === 'values') {
                    /* @var $values ElementValue[] */
                    $values = $value->getValues();
                    foreach ($values as $value) {
                        $result[$field][$value->getField()->getCode()] = $value;
                    }
                }
                else {
                    $result[$field] = $value->getValues();
                }
            }
            else {
                $result[$field] = $value;
            }
        }

        $result['isActive'] = $this->isActive();

        return $result;
    }
}
