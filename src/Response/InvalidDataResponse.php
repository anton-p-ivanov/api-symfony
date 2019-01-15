<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class InvalidDataResponse
 * @package App\Response
 */
class InvalidDataResponse extends JsonResponse
{
    /**
     * @var int
     */
    protected $statusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * @var array
     */
    protected $data = [
        'error' => 'validation_error',
        'description' => 'There are invalid data in client request.',
        'errors' => []
    ];

    /**
     * InvalidDataResponse constructor.
     *
     * @param array $errors
     */
    public function __construct($errors = [])
    {
        $this->data['errors'] = $errors;

        parent::__construct($this->data, $this->statusCode);
    }
}