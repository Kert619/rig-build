<?php

namespace App\Http\Controllers;

use App\Models\ScraperLog;

class ScraperLogController extends Controller
{
    protected string $modelClass = ScraperLog::class;

    protected function columns(): array
    {
        return ['scraper_log_id', 'scraper_id', 'started', 'ended', 'error_message', 'price_count'];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function updateRules($id): array
    {
        return [];
    }

    protected function with(): array
    {
        return ['scraper:scraper_id,scraper_name'];
    }

    public function truncate()
    {
        ScraperLog::query()->truncate();
        return response()->noContent();
    }
}
