<?php

namespace App\Contracts;

interface ScraperInterface
{
    public function scrape();

    public function preview();

    public function processCategories();
}
