<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPhoneNumbersValidator extends ConstraintValidator
{
    public function validate($phoneNumbers, Constraint $constraint)
    {
        foreach ($phoneNumbers as $phoneNumber) {

            $depth = 0;
            $digitCount = 0;

            for ($i = 0; $i < strlen($phoneNumber); $i++) {

                if (is_numeric($phoneNumber[$i])) {
                    $digitCount++;
                }

                if ($phoneNumber[$i] == '(') {
                    $depth++;
                }

                elseif ($phoneNumber[$i] == ')') {
                    $depth--;
                }

                if ($depth < 0) {
                    $this->context->buildViolation($constraint->message)
                                  ->addViolation();
                    return;
                }
            }

            if ($digitCount < 7 || $depth != 0) {
                $this->context->buildViolation($constraint->message)
                              ->addViolation();
                return;
            }

            $expr = '/^(\+{1}|\(\+{1})?\(?([0-9]{1,})\)?[-\ ]?\(?([0-9]{1,})\)?[-\ ]?\(?([0-9]{1,})\)?$/';

            if (!preg_match($expr, $phoneNumber)) {
                $this->context->buildViolation($constraint->message)
                              ->addViolation();
                return;
            }
        }
    }
}
