<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExistEntity extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Entity does not exist.';

    /**
     * @var string
     */
    public $multipleMessage = 'One or more entities do not exist.';
    
    /**
     * @var string
     */
    public $entityClass;

    /**
     * @var string
     */
    public $entityAttribute;

    /**
     * @return array
     */
    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }
}