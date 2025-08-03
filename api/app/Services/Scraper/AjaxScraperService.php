<?php

namespace App\Services\Scraper;

use App\Events\ScraperFinished;
use App\Events\ScraperStarted;
use App\Models\Scraper;
use App\Utils\TsvGenerator;
use Illuminate\Support\Facades\Log;

class AjaxScraperService extends BaseScraperService
{
    public function __construct(Scraper $scraper)
    {
        parent::__construct($scraper);
    }

    public function scrape()
    {
        ScraperStarted::dispatch(array_merge($this->scraper->toArray(), ['started' => now()]));

        $products = [];
        $errorMessage = null;
        try {
            $html = $this->fetchSingle($this->scraper->scraper_url);
            $categories = $this->categoryService->getCategoryLinks($html);
            $apiResponseGenerator = $this->categoryService->fetchCategoryPagesAjax($categories['category_links']);
            $products = $this->productService->getProductsAjax($apiResponseGenerator);
            TsvGenerator::generate($products, $this->scraper->scraper_name);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            Log::error('Scraper failed', [
                'scraper_id' => $this->scraper->id,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        } finally {
            ScraperFinished::dispatch(array_merge($this->scraper->toArray(), ['price_count' => count($products), 'error_message' => $errorMessage, 'ended' => now()]));
            gc_collect_cycles();
        }
    }

    public function preview()
    {
        $html = $this->fetchSingle($this->scraper->scraper_url);
        $categories = $this->categoryService->getCategoryLinks($html);

        foreach ($categories['category_links'] as $categoryLink) {
            $apiResponseGenerator = $this->categoryService->fetchCategoryPagesAjax([$categoryLink]);
            $products = $this->productService->getProductsAjax($apiResponseGenerator, true);
            if (count($products)) return $products;
        }
    }
}
