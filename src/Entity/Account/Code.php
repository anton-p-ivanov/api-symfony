<?php

namespace App\Entity\Account;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="accounts_codes")
 * @ORM\Entity()
 */
class Code
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=60, options={"fixed" = true})
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isExpired;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account\Account", inversedBy="codes")
     * @ORM\JoinColumn(name="account_uuid", referencedColumnName="uuid", nullable=false)
     */
    private $account;

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
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    /**
     * @return bool|null
     */
    public function getIsExpired(): ?bool
    {
        return $this->isExpired;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isExpired !== true && ($this->expiredAt === null || $this->expiredAt > new \DateTime());
    }
}
