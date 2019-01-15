<?php

namespace App\Form\Profile;

use App\Validator\Constraint as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Reset
 *
 * @package App\Form\Profile
 */
class Reset
{
    /**
     * @var null|string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @CustomAssert\ExistEntity(
     *     entityClass="App\Entity\User\User",
     *     message="constraint.user.email.not_found"
     * )
     */
    private $email;

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
}