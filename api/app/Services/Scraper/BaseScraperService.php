<?php

namespace App\Services\Scraper;

use App\Contracts\ScraperInterface;
use App\Models\Scraper;
use App\Traits\ScrapePage;
use Illuminate\Support\Facades\Log;

abstract class BaseScraperService implements ScraperInterface
{
    use ScrapePage;
    protected CategoryService $categoryService;
    protected ProductService $productService;
    protected Scraper $scraper;

    protected function __construct(Scraper $scraper)
    {
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

    public function processCategories()
    {
        $html = $this->fetchSingle($this->scraper->scraper_url);
        $categories = $this->categoryService->getCategoryLinks($html);

        $result = array_map(fn($link, $name) => ['category_link' => $link, 'category_name' => $name], $categories['category_links'], $categories['category_names']);

        return $result;
    }
}
