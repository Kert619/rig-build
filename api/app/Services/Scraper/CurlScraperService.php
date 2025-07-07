<?php

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Log;

class CurlScraperService extends BaseScraperService
{
    private CategoryService $categoryService;
    private ProductService $productService;

    public function __construct(string $baseUrl)
    {
        parent::__construct($baseUrl);
        $this->categoryService = app()->make(CategoryService::class, [
            'baseUrl' => $baseUrl,
            'scraperConfig' => $this->scraperConfig
        ]);
        $this->productService = app()->make(ProductService::class, [
            'baseUrl' => $baseUrl,
            'scraperConfig' => $this->scraperConfig
        ]);
    }

    public function scrape(string $url)
    {
        $start = microtime(true);
        $html = $this->fetchCurl($url, $this->baseUrl);
        $categories = $this->categoryService->getCategoryLinks($html);
        $end = microtime(true);
        Log::info($end - $start);
    }
}
