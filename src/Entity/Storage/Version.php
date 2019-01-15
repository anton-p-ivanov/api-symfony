<?php

namespace App\Entity\Storage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="storage_versions")
 * @ORM\Entity()
 */
class Version
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\File", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="file_uuid", referencedColumnName="uuid", onDelete="CASCADE")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Storage\Storage", inversedBy="versions")
     * @ORM\JoinColumn(name="storage_uuid", referencedColumnName="uuid", onDelete="CASCADE")
     */
    private $storage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return bool
     */
    protected function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return Storage|null
     */
    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive === true;
    }
}
