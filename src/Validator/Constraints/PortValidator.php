<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:08.
 */

namespace Jinya\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PortValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/\\d{1,5}/', $value) || ((int) $value < 0 && (int) $value > 65535)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
