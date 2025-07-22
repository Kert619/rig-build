<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $primaryKey = 'store_id';
    protected $fillable = ['store_name', 'country_code', 'store_url'];

    public function scrapers(): HasMany
    {
        return $this->hasMany(Scraper::class, 'store_id', 'store_id');
    }
}
