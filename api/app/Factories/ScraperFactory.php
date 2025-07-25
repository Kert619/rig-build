<?php

namespace App\Factories;

use App\Contracts\ScraperInterface;
use App\Models\Scraper;
use App\Services\Scraper\AjaxScraperService;
use App\Services\Scraper\CurlScraperService;
use App\Services\Scraper\PuppeteerScraperService;
use InvalidArgumentException;

class ScraperFactory
{
    public static function make(int $scraperId): ScraperInterface
    {
        $scraper = Scraper::query()->findOrFail($scraperId);

        return match ($scraper->scraper_config['settings']) {
            'puppeteer' => new PuppeteerScraperService($scraper),
            'ajax' => new AjaxScraperService($scraper),
            'curl' => new CurlScraperService($scraper),
            default => throw new InvalidArgumentException('Invalid scraper type')
        };
    }
}
