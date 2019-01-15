<?php

namespace App\Exceptions\OAuth;

use App\Exceptions\JsonExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * Class OAuthException
 * @package App\Exceptions\OAuth
 */
class OAuthException extends \Exception implements JsonExceptionInterface
{
    /**
     * Exception constructor.
     *
     * @param int $statusCode
     * @param string|null $message
     * @param string|null $description
     * @param \Exception|null $previous
     */
    public function __construct(int $statusCode, string $message = null, string $description = null, \Exception $previous = null)
    {
        $message = [
            'error' => $message,
            'error_description' => $description
        ];

        parent::__construct(json_encode($message), $statusCode, $previous);
    }
}