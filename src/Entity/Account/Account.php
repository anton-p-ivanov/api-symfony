<?php

namespace App\Entity\Account;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="App\Repository\Account\AccountRepository")
 */
class Account implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $web;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="integer", options={"default":100})
     */
    private $sort;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Account\Code", mappedBy="account")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $codes;

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
     * @return bool
     */
    protected function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return null|string
     */
    public function getWeb(): ?string
    {
        return $this->web;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return Collection
     */
    public function getCodes(): ?Collection
    {
        return $this->codes;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
        ];
    }
}