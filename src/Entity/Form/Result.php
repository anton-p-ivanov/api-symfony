<?php

namespace App\Entity\Form;

use App\Traits\WorkflowTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forms_results")
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Listener\WorkflowListener"})
 */
class Result implements \JsonSerializable
{
    use WorkflowTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form\Form")
     * @ORM\JoinColumn(name="form_uuid", referencedColumnName="uuid")
     */
    private $form;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form\Status")
     * @ORM\JoinColumn(name="status_uuid", referencedColumnName="uuid")
     */
    private $status;

    /**
     * @ORM\Column(type="text")
     */
    private $data;

    /**
     * @var array|string
     */
    private $rawData;

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param Form $form
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string|array $data
     */
    public function setData($data): void
    {
        $this->rawData = $data;
        $this->data = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array|string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        $serializableFields = [
            'uuid',
        ];

        foreach ($serializableFields as $field) {
            $value = $this->{"get" . ucfirst($field)}();
            $result[$field] = $value;
        }

        return $result;
    }
}
