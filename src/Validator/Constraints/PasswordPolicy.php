<?php
// src/Validator/Constraints/PasswordPolicy.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordPolicy extends Constraint
{
    public $message = 'Your password must contain at least 1 letter, 1 digit, and include one of -, _, or . characters.';
}
