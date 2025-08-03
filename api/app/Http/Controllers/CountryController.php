<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    protected string $modelClass = Country::class;
    protected string $optionColumn = "country_name";

    protected function columns(): array
    {
        return ['country_code', 'country_name'];
    }

    protected function rules(): array
    {
        return [
            'country_code' => 'required|string|max:2|unique:countries,country_code',
            'country_name' => 'required|string|max:255|unique:countries,country_name',
        ];
    }

    protected function updateRules($id): array
    {
        return [
            'country_name' => 'required|string|max:255|unique:countries,country_name,' . $id . ',country_code',
        ];
    }
}
