<?php

namespace App\Exceptions;

use Exception;

class ScraperCategoryLinksNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Category links not found in the HTML', 400);
    }
}
