<?php

namespace App\Form\Profile;

use App\Validator\Constraint\Checkword;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Confirm
 *
 * @package App\Form\Profile
 */
class Confirm
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="8")
     * @Checkword()
     */
    private $code;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="100")
     */
    private $password;

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Confirm
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return Confirm
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}