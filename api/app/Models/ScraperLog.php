<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScraperLog extends Model
{
    protected $primaryKey = 'scraper_log_id';
    protected $fillable = ['scraper_id', 'started', 'ended', 'error_message', 'price_count'];
    public $timestamps = false;

    public function scraper(): BelongsTo
    {
        return $this->belongsTo(Scraper::class, 'scraper_id', 'scraper_id');
    }

    protected function casts(): array
    {
        return [
            'started' => 'datetime:Y-m-d h:i:s A',
            'ended' => 'datetime:Y-m-d h:i:s A',
        ];
    }
}
