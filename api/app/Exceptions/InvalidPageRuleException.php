<?php

namespace App\Exceptions;

use Exception;

class InvalidPageRuleException extends Exception
{
    public function __construct()
    {
        parent::__construct('Page rule is invalid', 400);
    }
}
