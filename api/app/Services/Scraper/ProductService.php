<?php

namespace App\Services\Scraper;

use App\Exceptions\InvalidPageRegexException;
use App\Exceptions\InvalidPageRuleException;
use App\Exceptions\InvalidPageRuleFormatException;
use App\Exceptions\InvalidPageRuleGroupException;
use App\Traits\ScrapePage;
use App\Utils\ScraperUtils;
use Generator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ProductService
{
    use ScrapePage;
    private string $baseUrl;
    private array $scraperConfig;
    public function __construct(string $baseUrl, array $scraperConfig)
    {
        $this->baseUrl = $baseUrl;
        $this->scraperConfig = $scraperConfig;
    }
    public function getProductsPuppeteer(Generator $categoriesHtml, $preview = false)
    {
        $products = [];

        foreach ($categoriesHtml as  $categoryLink => $categoryHtml) {
            if (empty($categoryHtml)) continue;

            $paginationUrls = [];

            //only get the pagination if it's not a preview
            if (!$preview) {
                $paginationUrls = array_chunk($this->getPaginationUrls($categoryHtml, $categoryLink), 5);
            }

            $allPagesHtml = [$categoryHtml];

            // If there are pagination URLs, fetch the pages
            if (!empty($paginationUrls)) {
                foreach ($paginationUrls as $chunk) {
                    $pages = $this->fetchMulti($chunk);

                    if (!empty($pages->content)) {
                        array_push($allPagesHtml, ...$pages->content);
                    }
                }
            }

            // Process all pages (main + paginated)
            foreach ($allPagesHtml as $pageHtml) {
                $productContainerHtml = $this->getProductContainerHtml($pageHtml);
                $productHtmls = $this->getProductsHtml($productContainerHtml);
                foreach ($productHtmls as $product) {
                    $products[] = $this->getPageRuleValues($product);
                }
            }
        }

        $products = $this->normalizeProducts($products, $this->baseUrl);

        return $products;
    }

    public function getProductsAjax(Generator $apiResponses, $preview = false)
    {
        $products = [];
        foreach ($apiResponses as $apiUrl => $apiResponse) {
            if (empty($apiResponse)) continue;

            $paginationUrls = [];

            if (!$preview) {
                $parsedUrl = parse_url($apiUrl);
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

                $paginationUrls = $this->getPaginationUrls($apiResponse, $baseUrl);
                $paginationUrls = $this->combineApiQueryParams($apiUrl, $paginationUrls);
            }

            $allApiResponses = [$apiResponse];

            if (!empty($paginationUrls)) {
                foreach ($paginationUrls as $paginationUrl) {
                    $allApiResponses[] = $this->fetchCurl($paginationUrl, $this->baseUrl);
                }
            }

            // Process all pages (main + paginated)
            foreach ($allApiResponses as $response) {
                $productContainerHtml = $this->getProductContainerHtml($response);

                $productHtmls = $this->getProductsHtml($productContainerHtml);
                foreach ($productHtmls as $product) {
                    $products[] = $this->getPageRuleValues($product);
                }
            }
        }

        $products = $this->normalizeProducts($products, $this->scraperConfig['product']['ajax']['product_link_base_url']);

        return $products;
    }

    private function getProductContainerHtml(string $html)
    {
        $containerRegex = $this->scraperConfig['product']['container_regex'];
        $containerSelector = $this->scraperConfig['product']['container_selector'];

        if ($containerRegex) {
            return $this->extractHtml($html, $containerRegex);
        } else if ($containerSelector) {
            $crawler = new Crawler($html);
            $products = $crawler->filter($containerSelector)->each(
                fn(Crawler $node) =>
                $node->getNode(0)->ownerDocument->saveHTML($node->getNode(0))
            );

            return implode("\n", $products);
        }

        return '';
    }

    private function getProductsHtml(string $html)
    {
        $productRegex = $this->scraperConfig['product']['regex'];
        $productSelector = $this->scraperConfig['product']['selector'];

        if ($productRegex) {
            $matches = [];
            $this->extractHtml($html, $productRegex, $matches);

            return $matches[0];
        } else if ($productSelector) {
            $crawler = new Crawler($html);
            $products = $crawler->filter($productSelector)->each(
                fn(Crawler $node) =>
                $node->getNode(0)->ownerDocument->saveHTML($node->getNode(0))
            );

            return $products;
        }

        return [];
    }

    private function getPageRuleValues(string $productHtml)
    {
        $pageRules = $this->scraperConfig['product']['page_rules'];
        $format = $this->scraperConfig['product']['format'];

        $values = [];

        foreach ($format as $field => $formatValue) {
            if (!$formatValue || $field == 'currency') continue;

            [$formatIndex, $group] = $this->extractFormatIndexes($formatValue);

            $pageRule = $this->getPageRule($pageRules, $formatIndex);

            $values[$field] = $this->extractFieldValue($pageRule, $productHtml, $group);
        }

        return $values;
    }

    private function extractFormatIndexes(string $formatValue)
    {
        preg_match('%(\d).*?,.*?(\d)%usi', $formatValue, $matches);

        if (empty($matches)) {
            throw new InvalidPageRuleFormatException();
        }

        $formatIndex = (int)$matches[1] - 1;
        $group = (int)$matches[2];

        return [$formatIndex, $group];
    }

    private function getPageRule(array $pageRules, int $index)
    {
        if (!isset($pageRules[$index])) {
            throw new InvalidPageRuleException();
        }

        return $pageRules[$index]['value'];
    }

    private function extractFieldValue(string $pageRule, string $html, int $group)
    {

        $matches = [];

        $matchCount = preg_match($pageRule, $html, $matches);
        if ($matchCount == 0) {
            return '';
        }

        if (!isset($matches[$group])) {
            throw new InvalidPageRuleGroupException();
        }

        return ScraperUtils::cleanText($matches[$group]);
    }

    public function getPaginationUrls(string $html, string $categoryLink)
    {
        $method = $this->scraperConfig['product']['pagination']['method'];
        $containerRegex = $this->scraperConfig['product']['pagination']['container_regex'];
        $containerSelector = $this->scraperConfig['product']['pagination']['container_selector'];

        if ($method == 'regex') {
            if (!$containerRegex) return [];
        } else if ($method == 'selector') {
            if (!$containerSelector) return [];
        }

        $paginationContainerHtml = '';

        if ($method == 'regex') {
            $paginationContainerHtml = $this->extractHtml($html, $containerRegex);
        } else if ($method == 'selector') {
            $crawler = new Crawler($html);
            $pagination = $crawler->filter($containerSelector)->each(
                fn(Crawler $node) =>
                $node->getNode(0)->ownerDocument->saveHTML($node->getNode(0))
            );

            $paginationContainerHtml = implode("\n", $pagination);
        }

        if (!$paginationContainerHtml) return [];

        $pageNumbers = $this->getPaginationPages($paginationContainerHtml);
        $pageQuery = $this->scraperConfig['product']['pagination']['page_query'];
        $querySeparator = $this->scraperConfig['product']['pagination']['query_separator'];

        if (substr($categoryLink, -1) != $querySeparator) {
            $categoryLink .= $querySeparator;
        }

        $urls = array_map(fn($page) => $categoryLink . $pageQuery . $page, $pageNumbers);
        return $urls;
    }

    private function getPaginationPages(string $html)
    {
        try {
            $matches = [];
            $this->extractHtml($html, $this->scraperConfig['product']['pagination']['pages_regex'], $matches);

            if (!isset($matches[1])) {
                throw new InvalidPageRegexException();
            }

            if (empty($matches[1])) return [];

            $pageNumbers = array_filter($matches[1], fn($page) => is_numeric($page));

            $maxPage = (int) max($pageNumbers);

            return range(2, $maxPage);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            throw $th;
        }
    }

    public function combineApiQueryParams(string $sourceUrl, array $targetUrls)
    {
        if (empty($targetUrls)) return [];

        $newUrls = [];

        $sourceParts = parse_url($sourceUrl);
        foreach ($targetUrls as $targetUrl) {
            $targetParts = parse_url($targetUrl);

            // Parse query params
            parse_str($sourceParts['query'] ?? '', $sourceParams);
            parse_str($targetParts['query'] ?? '', $targetParams);

            // Merge, keeping existing keys in $targetParams (like page)
            $mergedParams = $targetParams + $sourceParams;
            // Rebuild query string
            $mergedQuery = http_build_query($mergedParams);

            $finalUrl = $targetParts['scheme'] . '://' . $targetParts['host'] . $targetParts['path'] . '?' . $mergedQuery;
            $newUrls[] = $finalUrl;
        }

        return $newUrls;
    }

    private function normalizeProducts(array $products, string $baseUrl)
    {
        $currency = $this->scraperConfig['product']['format']['currency'];

        foreach ($products as &$product) {
            if (isset($product['price_url'])) {
                $product['price_url'] = ScraperUtils::prependBaseUrlIfMissing($baseUrl, $product['price_url']);
            }

            if (isset($product['price'])) {
                $product['price'] = ScraperUtils::normalizePrice($product['price']);
            } else {
                $product['price'] = 0;
            }

            $product['currency'] = $currency;
        }

        return $products;
    }
}
