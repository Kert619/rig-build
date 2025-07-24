<?php

namespace App\Http\Controllers;

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
            'scraper_config.category.container_regex' => 'required|string',
            'scraper_config.category.regex' => 'required|string',
            'scraper_config.product.container_regex' => 'required_if:scraper_config.product.method,regex',
            'scraper_config.product.regex' => 'required_if:scraper_config.product.method,regex',
            'scraper_config.product.container_selector' => 'required_if:scraper_config.product.method,selector',
            'scraper_config.product.selector' => 'required_if:scraper_config.product.method,selector'
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
            'scraper_config.product.method' => 'method'
        ];
    }
}
