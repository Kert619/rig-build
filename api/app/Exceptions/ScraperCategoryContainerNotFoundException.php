<?php

namespace App\Exceptions;

use Exception;

class ScraperCategoryContainerNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Category container not found in the HTML', 400);
    }
}
