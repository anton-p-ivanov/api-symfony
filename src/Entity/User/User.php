<?php

namespace App\Entity\User;

use App\Entity\Role;
use App\Entity\Site;
use App\Traits\WorkflowTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\Listener\WorkflowListener"})
 *
 * @UniqueEntity(fields={"email"}, message="constraint.user.email_already_taken")
 */
class User implements UserInterface, \JsonSerializable
{
    use WorkflowTrait;

    /**
     * Scenarios constants
     */
    const
        SCENARIO_USER_REGISTER  = 1,
        SCENARIO_USER_UPDATE    = 2,
        SCENARIO_USER_RESET     = 3;

    /**
     * @var int
     */
    public $scenario;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lname;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sname;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $fullName;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $isConfirmed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_uuid", referencedColumnName="uuid")}
     * )
     */
    private $roles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Site")
     * @ORM\JoinTable(
     *     name="users_sites",
     *     joinColumns={@ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="site_uuid", referencedColumnName="uuid")}
     * )
     */
    private $sites;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Account\Account")
     * @ORM\JoinTable(
     *     name="users_accounts",
     *     joinColumns={@ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="account_uuid", referencedColumnName="uuid")}
     * )
     */
    private $accounts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Password", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $passwords;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User\Checkword", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $checkwords;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->scenario = self::SCENARIO_USER_REGISTER;

        $defaults = [
            'isActive' => true,
            'isConfirmed' => false,
            'roles' => new ArrayCollection(),
            'sites' => new ArrayCollection(),
            'accounts' => new ArrayCollection(),
            'passwords' => new ArrayCollection(),
        ];

        foreach ($defaults as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getFname(): ?string
    {
        return $this->fname;
    }

    /**
     * @param string $fname
     */
    public function setFname(string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @return null|string
     */
    public function getLname(): ?string
    {
        return $this->lname;
    }

    /**
     * @param string $lname
     */
    public function setLname(string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @return null|string
     */
    public function getSname(): ?string
    {
        return $this->sname;
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
    public function getIsConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getPhoneMobile(): ?string
    {
        return $this->phone_mobile;
    }

    /**
     * @return null|string
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return null|string
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        if ($this->password === null && $userPassword = $this->getUserPassword()) {
            $this->password = $userPassword->getPassword();
        }

        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword(?string $password): self
    {
        if ($password) {
            $userPassword = new Password();
            $userPassword->setPassword($password);
            $userPassword->setUser($this);

            $this->getPasswords()->add($userPassword);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        if ($this->salt === null && $userPassword = $this->getUserPassword()) {
            $this->salt = $userPassword->getSalt();
        }

        return $this->salt;
    }

    /**
     * @return ArrayCollection
     */
    public function getPasswords(): ?Collection
    {
        return $this->passwords;
    }

    /**
     * @return Password|null
     */
    public function getUserPassword(): ?Password
    {
        $criteria = Criteria::create()->orderBy(['createdAt' => 'DESC']);
        $userPassword = $this->getPasswords()->matching($criteria)->first();

        return $userPassword ?: null;
    }

    /**
     * @return ArrayCollection
     */
    public function getCheckwords(): ?Collection
    {
        return $this->checkwords;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param ArrayCollection $roles
     */
    public function setRoles(ArrayCollection $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return Collection
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    /**
     * @param ArrayCollection $sites
     */
    public function setSites(ArrayCollection $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccounts(): ArrayCollection
    {
        return $this->accounts;
    }

    /**
     * @param ArrayCollection $accounts
     */
    public function setAccounts(ArrayCollection $accounts): void
    {
        $this->accounts = $accounts;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        /* Nothing yet here */
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'fname' => $this->fname,
            'lname' => $this->lname,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'isActive' => $this->isActive,
            'isConfirmed' => $this->isConfirmed,
            'isExpired' => $this->getUserPassword() ? $this->getUserPassword()->isExpired() : true,
            'roles' => $this->getRoles()->map(function (Role $role) { return $role->getCode(); })->toArray(),
            'sites' => $this->getSites()->map(function (Site $site) { return $site->getUuid(); })->toArray(),
        ];
    }
}
