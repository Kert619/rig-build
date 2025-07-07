<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class TsvGenerator
{
    public static function generate(array $data, string $filename, $directory = 'tsv')
    {
        $filename = preg_replace('/\s+/', '_', strtolower($filename));
        $fields = [
            'price_store_ident',
            'price_name',
            'price',
            'stock_status',
            'stock_quantity',
            'rating',
            'price_url',
            'img_url',
            'currency'
        ];

        // Ensure directory exists
        Storage::makeDirectory($directory);

        // Full path (private, not web-accessible)
        $path = storage_path("app/private/{$directory}/{$filename}.tsv");

        $handle = fopen($path, 'w');

        if (!empty($data)) {
            // Write header
            fputcsv($handle, $fields, "\t");

            //write rows
            foreach ($data as $row) {
                $line = [];
                foreach ($fields as $field) {
                    $line[] = $row[$field] ?? ''; // fallback to empty string if key is missing
                }

                fputcsv($handle, $line, "\t");
            }
        }

        fclose($handle);

        return $path;
    }
}
