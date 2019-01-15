<?php

namespace App\Traits;

use App\Entity\Workflow;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait WorkflowTrait
 * @package App\Traits
 */
trait WorkflowTrait
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Workflow", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="workflow_uuid", referencedColumnName="uuid", nullable=true, onDelete="SET NULL")
     */
    private $workflow;

    /**
     * @return Workflow|null
     */
    public function getWorkflow(): ?Workflow
    {
        return $this->workflow;
    }

    /**
     * @param mixed $workflow
     *
     * @return mixed
     */
    public function setWorkflow(?Workflow $workflow): self
    {
        $this->workflow = $workflow;

        return $this;
    }
}