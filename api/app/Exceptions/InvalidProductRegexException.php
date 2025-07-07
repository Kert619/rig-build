<?php

namespace App\Exceptions;

use Exception;

class InvalidProductRegexException extends Exception
{
    public function __construct()
    {
        parent::__construct('Product regex is invalid', 400);
    }
}
