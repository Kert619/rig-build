<?php

namespace App\Listeners;

use App\Events\ScraperFinished;
use App\Models\ScraperLog;

class LogScraperResult
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScraperFinished $event): void
    {
        ScraperLog::query()
            ->where('scraper_id', $event->scraper['scraper_id'])
            ->where('ended', null)
            ->update([
                'ended' => $event->scraper['ended'],
                'price_count' => $event->scraper['price_count'],
                'error_message' => $event->scraper['error_message'],
            ]);
    }
}
