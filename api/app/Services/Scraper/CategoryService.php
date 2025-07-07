<?php

namespace App\Services\Scraper;

use App\Exceptions\ScraperCategoryContainerNotFoundException;
use App\Exceptions\ScraperCategoryLinksNotFoundException;
use App\Traits\ScrapePage;
use App\Utils\ScraperUtils;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    use ScrapePage;

    private string $baseUrl;
    private array $scraperConfig;

    public function __construct(string $baseUrl,  array $scraperConfig)
    {
        $this->baseUrl = $baseUrl;
        $this->scraperConfig = $scraperConfig;
    }
    public function getCategoryLinks(string $html)
    {
        $categoryContainerHtml = $this->extractHtml($html, $this->scraperConfig['category']['container_regex']);
        if (!$categoryContainerHtml) {
            throw new ScraperCategoryContainerNotFoundException();
        }

        $matches = [];
        $categoriesHtml = $this->extractHtml($categoryContainerHtml, $this->scraperConfig['category']['regex'], $matches);

        if (!$categoriesHtml) {
            throw new ScraperCategoryLinksNotFoundException();
        }

        if (!isset($matches[1]) || !isset($matches[2])) {
            throw new ScraperCategoryLinksNotFoundException();
        }

        $categoryLinks = array_map(fn($categoryLink) => ScraperUtils::prependBaseUrlIfMissing($this->baseUrl, $categoryLink), $matches[1]);

        return ['category_links' => $categoryLinks, 'category_names' => $matches[2]];
    }


    public function fetchCategoryPages(array $categoryLinks)
    {
        $categoryLinksChunk = array_chunk($categoryLinks, 10);

        foreach ($categoryLinksChunk as $chunk) {
            $result = $this->fetchMulti($chunk);
            foreach ($result->results as $categoryHtml) {
                yield $categoryHtml;
            }

            foreach ($result->errors as $error) {
                Log::info((array) $error);
            }
        }
    }

    public function fetchCategoryPagesAjax(array $categoryLinks)
    {
        foreach ($categoryLinks as $categoryLink) {
            $api = $this->fetchAjax($categoryLink, $this->scraperConfig['product']['ajax']['api_base_url']);
            $response = $this->fetchCurl($api, $this->baseUrl);
            yield $api => $response;
            sleep(rand(1, 3));
        }
    }
}
