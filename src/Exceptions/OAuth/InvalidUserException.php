<?php

namespace App\Exceptions\OAuth;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidUserException
 * @package App\Exceptions\OAuth
 */
class InvalidUserException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'invalid_user';

    /**
     * @var string
     */
    protected $description = 'User not found.';

    /**
     * Exception constructor.
     *
     * @param string|null $description
     */
    public function __construct(string $description = null)
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $this->message, $description ?? $this->description);
    }
}