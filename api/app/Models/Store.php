<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $primaryKey = 'store_id';
    protected $fillable = ['store_name', 'country_code', 'store_url'];
}
