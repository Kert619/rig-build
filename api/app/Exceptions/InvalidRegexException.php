<?php

namespace App\Exceptions;

use Exception;

class InvalidRegexException extends Exception
{
    public function __construct()
    {
        parent::__construct('Your regular expression is invalid', 400);
    }
}
