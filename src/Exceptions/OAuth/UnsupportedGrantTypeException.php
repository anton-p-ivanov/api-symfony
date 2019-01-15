<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\JsonExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UnsupportedGrantTypeException
 * @package App\Exceptions\OAuth
 */
class UnsupportedGrantTypeException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'unsupported_grant_type';

    /**
     * Exception constructor.
     *
     * @param string|null $description
     */
    public function __construct(string $description = null)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $this->message, $description);
    }
}