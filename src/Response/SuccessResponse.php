<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SuccessResponse
 * @package App\Response
 */
class SuccessResponse extends JsonResponse
{
    /**
     * @var int
     */
    protected $statusCode = JsonResponse::HTTP_OK;

    /**
     * @var array
     */
    protected $data = [
        'error' => false,
        'description' => null
    ];

    /**
     * SuccessResponse constructor.
     *
     * @param null $description
     */
    public function __construct($description = null)
    {
        $this->data['description'] = $description;

        parent::__construct($this->data, $this->statusCode);
    }
}