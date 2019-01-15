<?php

namespace App\Entity\Mail;

use App\Entity\Site;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="mail_templates")
 * @ORM\Entity()
 */
class Template
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string",  length=255, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $recipient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $replyTo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $copyTo;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textBody;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $htmlBody;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Site")
     * @ORM\JoinTable(name="mail_templates_sites",
     *     joinColumns={@ORM\JoinColumn(name="template_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="site_uuid", referencedColumnName="uuid")}
     * )
     */
    private $sites;

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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getSender(): ?string
    {
        return $this->sender;
    }

    /**
     * @return string|null
     */
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    /**
     * @return null|string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @return null|string
     */
    public function getCopyTo(): ?string
    {
        return $this->copyTo;
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
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return null|string
     */
    public function getTextBody(): ?string
    {
        return $this->textBody;
    }

    /**
     * @return null|string
     */
    public function getHtmlBody(): ?string
    {
        return $this->htmlBody;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive == 1;
    }
}
