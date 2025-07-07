<?php

namespace App\Factories;

use App\Contracts\ScraperInterface;
use App\Services\Scraper\AjaxScraperService;
use App\Services\Scraper\CurlScraperService;
use App\Services\Scraper\PuppeteerScraperService;
use InvalidArgumentException;

class ScraperFactory
{
    public static function make(string $baseUrl, string $type = 'puppeteer'): ScraperInterface
    {
        return match ($type) {
            'puppeteer' => new PuppeteerScraperService($baseUrl),
            'ajax' => new AjaxScraperService($baseUrl),
            'curl' => new CurlScraperService($baseUrl),
            default => throw new InvalidArgumentException('Invalid scraper type')
        };
    }
}
