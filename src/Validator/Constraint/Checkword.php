<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Checkword extends Constraint
{
    /**
     * @var string
     */
    public $invalid_message = 'constraint.checkword.is_invalid';

    /**
     * @var string
     */
    public $expired_message = 'constraint.checkword.is_expired';
}