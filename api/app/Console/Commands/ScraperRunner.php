<?php

namespace App\Console\Commands;

use App\Factories\ScraperFactory;
use App\Models\Scraper;
use Illuminate\Console\Command;

class ScraperRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all active scrapers';

    /**
     * Execute the console command.
     */
    public function handle(ScraperFactory $scraperFactory)
    {
        $this->info("Scraper started");

        $activeScrapers = Scraper::query()->where('is_active', true)->get();

        if ($activeScrapers->isEmpty()) {
            $this->error('There are no active scrapers');
            return;
        }

        foreach ($activeScrapers as $activeScraper) {
            $activeScraper->update(['is_running' => true]);
            $scraperFactory->make($activeScraper->scraper_id)->scrape();
            $activeScraper->update(['is_running' => false, 'last_run' => now()]);
        }

        $this->info("Scraper finished");
    }
}
