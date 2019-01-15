<?php

namespace App\Validator\Constraint;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExistEntityValidator
 * @package App\Validator\Constraint
 */
class ExistEntityValidator extends ConstraintValidator
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
     * @param string|array $value
     * @param ExistEntity|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $attribute = $constraint->entityAttribute;
        if (!$attribute) {
            $attribute = $this->context->getPropertyName();
        }

        $entities = $this->registry
            ->getRepository($constraint->entityClass)
            ->findBy([$attribute => $value]);

        if (is_array($value) && count($value) !== count($entities)) {
            $this->context->buildViolation($constraint->multipleMessage)->addViolation();
            return;
        }

        if (!array_shift($entities) instanceof $constraint->entityClass) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
    }
}
