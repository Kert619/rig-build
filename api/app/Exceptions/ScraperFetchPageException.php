<?php

namespace App\Exceptions;

use Exception;

class ScraperFetchPageException extends Exception
{
    public function __construct(int $code = 0, $message = 'Something went wrong!')
    {
        parent::__construct($message, $code);
    }
}
