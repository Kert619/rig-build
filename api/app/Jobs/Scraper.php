<?php

namespace App\Jobs;

use App\Factories\ScraperFactory;
use App\Models\Scraper as ScraperModel;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class Scraper implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $scraperId)
    {
        //
    }

    public function uniqueId(): string
    {
        return $this->scraperId;
    }

    /**
     * Execute the job.
     */
    public function handle(ScraperFactory $scraperFactory): void
    {
        $scraperModel = ScraperModel::query()->findOrFail($this->scraperId);
        try {
            $scraperModel->update(['is_running' => true, 'is_active' => true]);
            $scraper = $scraperFactory->make($this->scraperId);
            $scraper->scrape();
        } catch (\Throwable $th) {
            $this->fail($th);
        } finally {
            $scraperModel->update(['is_running' => false, 'last_run' => now()]);
        }
    }
}
