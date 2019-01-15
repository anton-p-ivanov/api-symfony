<?php

namespace App\Validator\Constraint;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckwordValidator
 * @package App\Validator\Constraint
 */
class CheckwordValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * ExistEntity constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $value
     * @param Checkword|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $checkword = $this->registry
            ->getRepository('App:User\Checkword')
            ->findOneBy(['checkword' => $value]);

        if (!$checkword) {
            $this->context->buildViolation($constraint->invalid_message)->addViolation();
        }

        if ($checkword && !$checkword->isValid()) {
            $this->context->buildViolation($constraint->expired_message)->addViolation();
        }
    }
}
