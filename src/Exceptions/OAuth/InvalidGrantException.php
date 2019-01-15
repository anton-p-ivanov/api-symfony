<?php

namespace App\Exceptions\OAuth;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidGrantException
 * @package App\Exceptions\OAuth
 */
class InvalidGrantException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'invalid_grant';

    /**
     * @var string
     */
    protected $description = 'The authorization code is invalid or expired.';

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