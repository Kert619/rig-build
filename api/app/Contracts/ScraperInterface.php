<?php

namespace App\Contracts;

interface ScraperInterface
{
    public function scrape(string $url);
}
