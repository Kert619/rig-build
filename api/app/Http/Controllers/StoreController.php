<?php

namespace App\Http\Controllers;

use App\Models\Store;

class StoreController extends Controller
{
    protected string $modelClass = Store::class;
    protected string $optionColumn = "store_name";

    public function columns(): array
    {
        return ['store_id', 'store_name', 'country_code', 'store_url'];
    }

    public function rules(): array
    {
        return [
            'store_name' => 'required|string|max:255|unique:stores,store_name',
            'country_code' => 'required|string|size:2|exists:countries,country_code',
            'store_url' => 'required|string|max:500|url'
        ];
    }

    public function updateRules($id): array
    {
        return [
            'store_name' => 'required|string|max:255|unique:stores,store_name,' . $id . ',store_id',
            'country_code' => 'required|string|size:2|exists:countries,country_code',
            'store_url' => 'required|string|max:500|url'
        ];
    }
}
