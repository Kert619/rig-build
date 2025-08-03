<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scraper extends Model
{
    protected $primaryKey = 'scraper_id';
    protected $fillable = ['scraper_name', 'store_id', 'scraper_url', 'scraper_config', 'is_running', 'is_active', 'last_run'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    public function scraperLogs(): HasMany
    {
        return $this->hasMany(ScraperLog::class, 'scraper_id', 'scraper_id');
    }

    protected function casts(): array
    {
        return [
            'is_running' => 'boolean',
            'is_active' => 'boolean',
            'scraper_config' => 'array'
        ];
    }
}
