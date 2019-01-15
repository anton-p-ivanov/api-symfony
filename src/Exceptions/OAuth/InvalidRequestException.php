<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\JsonExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidRequestException
 * @package App\Exceptions\OAuth
 */
class InvalidRequestException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'invalid_request';

    /**
     * @var string
     */
    protected $description = 'Invalid request.';

    /**
     * Exception constructor.
     *
     * @param string|null $description
     */
    public function __construct(string $description = null)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $this->message, $description ?? $this->description);
    }
}