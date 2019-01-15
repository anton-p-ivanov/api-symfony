<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class InvalidRequestResponse
 * @package App\Response
 */
class InvalidRequestResponse extends JsonResponse
{
    /**
     * @var int
     */
    protected $statusCode = JsonResponse::HTTP_BAD_REQUEST;

    /**
     * @var array
     */
    protected $data = [
        'error' => 'invalid_request',
        'error_description' => 'Client request could not be processed.'
    ];

    /**
     * InvalidRequestResponse constructor.
     */
    public function __construct()
    {
        parent::__construct($this->data, $this->statusCode);
    }
}