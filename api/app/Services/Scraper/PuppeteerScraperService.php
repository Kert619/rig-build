<?php

namespace App\Services\Scraper;

use App\Models\Scraper;
use App\Utils\TsvGenerator;

class PuppeteerScraperService extends BaseScraperService
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
        $html = $this->fetchSingle($this->scraper->scraper_url);
        $categories = $this->categoryService->getCategoryLinks($html);
        $categoriesGenerator = $this->categoryService->fetchCategoryPages($categories['category_links']);
        $products = $this->productService->getProductsPuppeteer($categoriesGenerator);
        gc_collect_cycles();

        TsvGenerator::generate($products, $this->scraper->scraper_name);
    }
}
