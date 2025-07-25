<?php

namespace App\Services\Scraper;

use App\Models\Scraper;
use Illuminate\Support\Facades\Log;

class CurlScraperService extends BaseScraperService
{
    private CategoryService $categoryService;
    private ProductService $productService;
    private Scraper $scraper;

    public function __construct(Scraper $scraper)
    {
        parent::__construct($scraper->scraper_url);
        $this->categoryService = app()->make(CategoryService::class, [
            'baseUrl' => $scraper->scraper_url,
            'scraperConfig' => $scraper->scraper_config
        ]);
        $this->productService = app()->make(ProductService::class, [
            'baseUrl' => $scraper->scraper_url,
            'scraperConfig' => $scraper->scraper_config
        ]);

        $this->scraper = $scraper;
    }


    public function scrape()
    {
        $start = microtime(true);
        $html = $this->fetchCurl($this->scraper->scraper_url, $this->baseUrl);
        $categories = $this->categoryService->getCategoryLinks($html);
        $end = microtime(true);
        Log::info($end - $start);
    }
}
