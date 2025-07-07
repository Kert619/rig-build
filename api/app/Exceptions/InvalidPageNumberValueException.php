<?php

namespace App\Exceptions;

use Exception;

class InvalidPageNumberValueException extends Exception
{
    public function __construct()
    {
        parent::__construct('Page number value is invalid', 400);
    }
}
