<?php

namespace App\Listeners;

use App\Events\ScraperStarted;
use App\Models\ScraperLog;

class LogScraperStarted
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
    public function handle(ScraperStarted $event): void
    {
        ScraperLog::create(['scraper_id' => $event->scraper['scraper_id'], 'started' => $event->scraper['started']]);
    }
}
