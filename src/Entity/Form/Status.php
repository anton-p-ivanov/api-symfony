<?php

namespace App\Entity\Form;

use App\Entity\Mail\Template;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forms_statuses")
 * @ORM\Entity()
 */
class Status
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDefault;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form\Form")
     * @ORM\JoinColumn(name="form_uuid", referencedColumnName="uuid")
     */
    private $form;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Mail\Template")
     * @ORM\JoinColumn(name="mail_template_uuid", referencedColumnName="uuid")
     */
    private $mailTemplate;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return Template|null
     */
    public function getMailTemplate(): ?Template
    {
        return $this->mailTemplate;
    }
}
