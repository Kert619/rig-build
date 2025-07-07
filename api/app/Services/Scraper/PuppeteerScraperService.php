<?php

namespace App\Services\Scraper;

use App\Utils\TsvGenerator;
use Illuminate\Support\Facades\Log;

class PuppeteerScraperService extends BaseScraperService
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
        $html = $this->fetchSingle($url);
        $categories = $this->categoryService->getCategoryLinks($html);
        $categoriesGenerator = $this->categoryService->fetchCategoryPages($categories['category_links']);
        $products = $this->productService->getProductsPuppeteer($categoriesGenerator);
        gc_collect_cycles();
        $end = microtime(true);
        Log::info($end - $start);
        Log::info($products);

        TsvGenerator::generate($products, $this->scraperConfig['scraper_name']);
    }
}
