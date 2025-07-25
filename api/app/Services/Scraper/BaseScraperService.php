<?php

namespace App\Services\Scraper;

use App\Contracts\ScraperInterface;
use App\Traits\ScrapePage;

abstract class BaseScraperService implements ScraperInterface
{
    use ScrapePage;
    protected string $baseUrl;
    protected array $scraperConfig = [];

    protected function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}
