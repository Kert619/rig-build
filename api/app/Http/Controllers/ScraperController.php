<?php

namespace App\Http\Controllers;

use App\Models\Scraper;

class ScraperController extends Controller
{
    protected string $modelClass = Scraper::class;

    public function columns(): array
    {
        return ['scraper_id', 'scraper_name', 'scraper_url', 'scraper_config', 'is_running', 'is_active', 'last_run'];
    }

    public function rules(): array
    {
        return [
            'scraper_name' => 'required|string|max:255|unique:scrapers,scraper_name',
            'scraper_url' => 'required|string|max:500|url',
        ];
    }

    public function updateRules($id): array
    {
        return [
            'scraper_name' => 'required|string|max:255|unique:scrapers,scraper_name,' . $id . ',scraper_id',
            'scraper_url' => 'required|string|max:500|url',
            'scraper_config' => 'required|string|json',
        ];
    }
}
