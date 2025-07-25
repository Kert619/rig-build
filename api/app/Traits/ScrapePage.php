<?php

namespace App\Traits;

use App\Exceptions\InvalidRegexException;
use App\Exceptions\ScraperFetchPageException;
use App\Utils\ScraperUtils;

trait ScrapePage
{
    protected function fetchMulti(array $urls)
    {
        $escapedUrls = array_map(fn($url) => escapeshellarg($url), $urls);
        $args = implode(' ', $escapedUrls);
        $scriptPath = base_path('app/puppeteer/multi/index.js');
        $command = "node $scriptPath $args";

        $result = json_decode(shell_exec($command));

        return $result;
    }

    protected function fetchSingle(string $url)
    {
        $url = escapeshellarg($url);
        $scriptPath = base_path('app/puppeteer/single/index.js');
        $command = "node $scriptPath $url";

        exec($command, $output, $resultCode);

        $result = implode("\n", $output);

        if ($resultCode == 0) {
            return $result;
        } else {
            $output = json_decode($result);
            throw new ScraperFetchPageException($output->status, $output->error);
        }
    }

    protected function fetchAjax(string $url, string $apiBaseUrl)
    {
        $url = escapeshellarg($url);
        $apiBaseUrl = escapeshellarg($apiBaseUrl);
        $scriptPath = base_path('app/puppeteer/ajax/index.js');
        $command = "node $scriptPath $url $apiBaseUrl";

        exec($command, $output, $resultCode);

        $result = implode("\n", $output);

        if ($resultCode == 0) {
            return $result;
        } else {
            $output = json_decode($result);
            throw new ScraperFetchPageException($output->status, $output->error);
        }
    }


    protected function fetchCurl(string $url, string $baseUrl)
    {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER  => [
                'User-Agent: ' . ScraperUtils::getRandomUserAgent(),
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: ' . rtrim($baseUrl, '/'),
                'Origin: ' . rtrim($baseUrl, '/'),
            ]
        ]);

        $response = curl_exec($curl);

        $httpStatus = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);

        if ($httpStatus != 200) {
            throw new ScraperFetchPageException($httpStatus, "Fetching using curl failed");
        }

        return $response;
    }

    private function extractHtml(string $html, string $pattern, &$matches = [])
    {
        try {
            $matchedCount = preg_match_all($pattern, $html, $matches);

            if ($matchedCount == 0) {
                return '';
            }

            $combinedHtml = implode("\n", $matches[0]);

            return ScraperUtils::decodeUnicodeEscapes($combinedHtml);
        } catch (\Throwable $e) {
            throw new InvalidRegexException();
        }
    }
}
