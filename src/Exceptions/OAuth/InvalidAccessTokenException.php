<?php

namespace App\Exceptions\OAuth;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidAccessTokenException
 * @package App\Exceptions\OAuth
 */
class InvalidAccessTokenException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'invalid_access_token';

    /**
     * @var string
     */
    protected $description = 'Access token is invalid or expired.';

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