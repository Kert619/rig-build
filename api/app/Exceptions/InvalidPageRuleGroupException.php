<?php

namespace App\Exceptions;

use Exception;

class InvalidPageRuleGroupException extends Exception
{
    public function __construct()
    {
        parent::__construct('Page rule group is invalid', 400);
    }
}
