<?php

namespace App\Utils;

class ScraperUtils
{
    public static function prependBaseUrlIfMissing(string $baseUrl, string $url)
    {
        // If the url doesn't contain the base URL, prepend it
        if (!str_contains($url, $baseUrl)) {
            // Ensure no double slashes when concatenating
            return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        }

        return trim(html_entity_decode($url));
    }

    public static function getRandomUserAgent()
    {
        $userAgents = config('useragents.user_agents');
        return $userAgents[array_rand($userAgents)];
    }

    public static function normalizePrice(string $price)
    {
        return trim(preg_replace('%^[^\d]+%', '', $price));
    }

    public static function decodeUnicodeEscapes($str)
    {
        $result = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            $binary = pack('H*', $match[1]);
            return mb_convert_encoding($binary, 'UTF-8', 'UCS-2BE');
        }, $str);

        return str_replace('\\', '', $result);
    }

    public static function cleanText(string $text)
    {
        $htmlDecoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

        $escaped =  json_encode($htmlDecoded);
        $decoded = json_decode($escaped);

        $cleaned = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $decoded);

        return $cleaned;
    }
}
