<?php

namespace App\Form\Profile;

use App\Validator\Constraint\ExistEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Register
 *
 * @package App\Form\Profile
 */
class Register
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\Length(min="8")
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\Length(min="10", max="10")
     */
    private $code;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="100")
     */
    private $fname;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="100")
     */
    private $lname;

    /**
     * @var string
     *
     * @Assert\Length(max="200")
     */
    private $position;

    /**
     * @var array
     *
     * @ExistEntity(entityClass="\App\Entity\Role", entityAttribute="code")
     */
    private $roles;

    /**
     * @var array
     *
     * @ExistEntity(entityClass="\App\Entity\Site", entityAttribute="uuid")
     */
    private $sites;

    /**
     * Register constructor.
     */
    public function __construct()
    {
        $attributes = [
            'sites' => [],
            'roles' => [],
            'password' => \App\Service\Password::generate()
        ];

        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }
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
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getSites(): array
    {
        return $this->sites;
    }

    /**
     * @param array $sites
     */
    public function setSites(array $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fname . ' ' . $this->lname;
    }
}