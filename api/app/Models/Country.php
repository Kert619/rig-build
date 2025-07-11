<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $primaryKey = 'country_code';
    protected $fillable = ['country_code', 'country_name'];
    public $incrementing = false;
}
