<?php

namespace App\Console\Commands;

use App\Factories\ScraperFactory;
use App\Models\Scraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $this->info('Scraper is running');

        $activeScrapers = Scraper::query()->where('is_active', true)->get();

        foreach ($activeScrapers as $activeScraper) {
            try {
                $scraperFactory->make($activeScraper->scraper_id)->scrape();
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
                throw $th;
            }
        }

        $this->info('Scraper has finished runing');
    }
}
