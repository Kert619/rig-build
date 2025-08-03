<?php

namespace App\Services\Scraper;

use App\Exceptions\ScraperCategoryContainerNotFoundException;
use App\Exceptions\ScraperCategoryLinksNotFoundException;
use App\Traits\ScrapePage;
use App\Utils\ScraperUtils;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

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
        $containerRegex = $this->scraperConfig['category']['container_regex'];
        $containerSelector = $this->scraperConfig['category']['container_selector'];

        $containerHtml = '';
        //get category container html using regex or selector
        if ($containerRegex) {
            $containerHtml = $this->extractHtml($html, $containerRegex);
        } else if ($containerSelector) {
            $crawler = new Crawler($html);
            $category = $crawler->filter($containerSelector)->each(
                fn(Crawler $node) =>
                $node->getNode(0)->ownerDocument->saveHTML($node->getNode(0))
            );

            $containerHtml = implode("\n", $category);
        }

        if (!$containerHtml) {
            throw new ScraperCategoryContainerNotFoundException();
        }

        $matches = [];
        $categoriesHtml = $this->extractHtml($containerHtml, $this->scraperConfig['category']['regex'], $matches);

        if (!$categoriesHtml) {
            throw new ScraperCategoryLinksNotFoundException();
        }

        if (!isset($matches[1]) || !isset($matches[2])) {
            throw new ScraperCategoryLinksNotFoundException();
        }

        $categoryLinks = array_map(fn($categoryLink) => ScraperUtils::prependBaseUrlIfMissing($this->baseUrl, $categoryLink), $matches[1]);
        $categoryNames = array_map(fn($categoryName) => ScraperUtils::cleanText($categoryName), $matches[2]);

        return ['category_links' => $categoryLinks, 'category_names' => $categoryNames];
    }


    public function fetchCategoryPages(array $categoryLinks)
    {
        foreach ($categoryLinks as $categoryLink) {
            yield $categoryLink => $this->fetchSingle($categoryLink);
        }
    }

    public function fetchCategoryPagesAjax(array $categoryLinks)
    {
        foreach ($categoryLinks as $categoryLink) {
            $apiUrl = $this->fetchAjax($categoryLink, $this->scraperConfig['product']['ajax']['api_base_url']);
            $response = $this->fetchCurl($apiUrl, $this->baseUrl);
            yield $apiUrl => $response;
            sleep(rand(1, 3));
        }
    }
}
