<?php

namespace App\Exceptions\OAuth;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidClientException
 * @package App\Exceptions\OAuth
 */
class InvalidClientException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'invalid_client';

    /**
     * @var string
     */
    protected $description = 'Invalid client.';

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