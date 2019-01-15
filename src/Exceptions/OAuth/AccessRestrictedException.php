<?php

namespace App\Exceptions\OAuth;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccessRestrictedException
 * @package App\Exceptions\OAuth
 */
class AccessRestrictedException extends OAuthException
{
    /**
     * @var string
     */
    protected $message = 'access_restricted';

    /**
     * @var string
     */
    protected $description = 'User does not have privileges to access this web site.';

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