<?php

namespace App\Entity\Form;

use App\Entity\Mail\Template;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forms")
 * @ORM\Entity()
 */
class Form
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Mail\Template")
     * @ORM\JoinColumn(name="mail_template_uuid", referencedColumnName="uuid")
     */
    private $mailTemplate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Field\Field")
     * @ORM\JoinTable(
     *     name="forms_fields",
     *     joinColumns={@ORM\JoinColumn(name="form_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="field_uuid", referencedColumnName="uuid")}
     * )
     */
    private $fields;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return Collection
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Template|null
     */
    public function getMailTemplate(): ?Template
    {
        return $this->mailTemplate;
    }
}
