<?php

namespace App\Exceptions;

use Exception;

class InvalidPageRegexException extends Exception
{
    public function __construct()
    {
        parent::__construct('Page link rule is invalid', 400);
    }
}
