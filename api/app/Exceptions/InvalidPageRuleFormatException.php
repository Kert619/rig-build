<?php

namespace App\Exceptions;

use Exception;

class InvalidPageRuleFormatException extends Exception
{
    public function __construct()
    {
        parent::__construct('Page rule format is invalid', 400);
    }
}
