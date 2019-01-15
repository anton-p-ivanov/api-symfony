<?php

namespace App\Service;

/**
 * Class Client
 *
 * Stores OAuth client unique identifier used in requests.
 *
 * @package App\Service
 */
class Client
{
    /**
     * @var null|string
     */
    private $uuid;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param null|string $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }
}