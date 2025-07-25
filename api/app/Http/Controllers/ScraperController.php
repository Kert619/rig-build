<?php

namespace App\Http\Controllers;

use App\Jobs\Scraper as JobsScraper;
use App\Models\Scraper;

class ScraperController extends Controller
{
    protected string $modelClass = Scraper::class;

    protected function columns(): array
    {
        return ['scraper_id', 'store_id', 'scraper_name', 'scraper_url', 'scraper_config', 'is_running', 'is_active', 'last_run'];
    }

    protected function rules(): array
    {
        return [
            'store_id' => 'required|exists:.stores,store_id',
            'scraper_name' => 'required|string|max:255|unique:scrapers,scraper_name',
            'scraper_url' => 'required|string|max:500|url',
        ];
    }

    protected function updateRules($id): array
    {
        return [
            'scraper_name' => 'required|string|max:255|unique:scrapers,scraper_name,' . $id . ',scraper_id',
            'scraper_url' => 'required|string|max:500|url',

            'scraper_config' => 'required|array',

            'scraper_config.settings' => 'required|in:puppeteer,ajax,curl',

            'scraper_config.category.container_regex' => 'required|string',
            'scraper_config.category.regex' => 'required|string',

            'scraper_config.product.method' => 'required|in:regex,selector',

            'scraper_config.product.container_regex' => 'required_if:scraper_config.product.method,regex',
            'scraper_config.product.regex' => 'required_if:scraper_config.product.method,regex',

            'scraper_config.product.container_selector' => 'required_if:scraper_config.product.method,selector',
            'scraper_config.product.selector' => 'required_if:scraper_config.product.method,selector',

            'scraper_config.product.format.price_store_ident' => 'nullable|string',
            'scraper_config.product.format.price_name' => 'nullable|string',
            'scraper_config.product.format.price' => 'nullable|string',
            'scraper_config.product.format.stock_status' => 'nullable|string',
            'scraper_config.product.format.stock_quantity' => 'nullable|string',
            'scraper_config.product.format.rating' => 'nullable|string',
            'scraper_config.product.format.price_url' => 'nullable|string',
            'scraper_config.product.format.img_url' => 'nullable|string',
            'scraper_config.product.format.currency' => 'required|string',

            'scraper_config.product.page_rules' => 'required|array',

            'scraper_config.product.pagination.container_regex' => 'nullable|string',
            'scraper_config.product.pagination.base_pagination_link' => 'nullable|string',
            'scraper_config.product.pagination.pages_regex' => 'nullable|string',
            'scraper_config.product.pagination.page_query' => 'nullable|string',

            'scraper_config.product.ajax.api_base_url' => 'nullable|string',
            'scraper_config.product.ajax.product_link_base_url' => 'nullable|string',
        ];
    }

    protected function fieldNames(): array
    {
        return [
            'scraper_config.category.container_regex' => 'category container regex',
            'scraper_config.category.regex' => 'category regex',
            'scraper_config.product.container_regex' => 'product container regex',
            'scraper_config.product.regex' => 'product regex',
            'scraper_config.product.container_selector' => 'product container selector',
            'scraper_config.product.selector' => 'product selector',
            'scraper_config.product.method' => 'method',
            'scraper_config.product.page_rules' => 'page rules',
            'scraper_config.product.format.currency' => 'currency',
        ];
    }

    public function scrape(int $scraperId)
    {
        JobsScraper::dispatch($scraperId);
        return response()->json(['message' => 'Scraper started']);
    }

    public function setActive(Scraper $scraper)
    {
        $scraper->update(['is_active' => !$scraper->is_active]);

        $active = $scraper->is_active ? 'active' : 'inactive';

        return response()->json(['message' => 'Scraper is set to ' . $active]);
    }
}
