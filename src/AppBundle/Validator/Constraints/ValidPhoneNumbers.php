<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidPhoneNumbers extends Constraint
{
    public $message = "Invalid phone number.";
}
