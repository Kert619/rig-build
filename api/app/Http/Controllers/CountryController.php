<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    protected string $modelClass = Country::class;

    public function columns(): array
    {
        return ['country_code', 'country_name'];
    }

    public function rules(): array
    {
        return [
            'country_code' => 'required|string|max:2|unique:countries,country_code',
            'country_name' => 'required|string|max:255,unique:countries,country_name',
        ];
    }

    public function updateRules(): array
    {
        return [
            'country_name' => 'required|string|max:255,unique:countries,country_name',
        ];
    }
}
