<?php

namespace App\Services\Scraper;

use App\Contracts\ScraperInterface;
use App\Traits\ScrapePage;

abstract class BaseScraperService implements ScraperInterface
{
    use ScrapePage;
    protected string $baseUrl;
    protected array $scraperConfig = [
        'scraper_name' => 'Villman Computers Scraper',
        'category' => [
            'container_regex' => '%nav-bar">.*?</nav>%usi',
            'regex' => '%<a\shref="(/collections/all)"[^>]*?>([^<]+).*?</a>%usi',
        ],
        'product' => [
            'method' => '',
            'regex' => '%product-item(?=\s)[^>]*?>.*?</form>%usi',
            'selector' => '',
            'format' => [
                'price_store_ident' => '{1,1}',
                'price_name' => '{2,1}',
                'price' => '{3,1}',
                'stock_status' => '{4,1}',
                'stock_quantity' => '',
                'rating' => '',
                'price_url' => '{5,1}',
                'img_url' => '{6,1}',
                'currency' => 'PHP'
            ],
            'page_rules' => [
                '%<input.*?name="id".*?value="(.*?)">%usi',
                '%product-item__title[^>]*?>(.*?)</a>%usi',
                '%class="(?:price(?!\s)|price\sprice--highlight)[^>]*?>(.*?)</span>%usi',
                '%class="product-item__inventory[^>]*?>(.*?)</span>%usi',
                '%href="([^"]*)"[^>]*class="product-item__title%usi',
                '%class="product-item__primary-image.*?src="(.*?)"%usi'
            ],
            'pagination' => [
                'container_regex' => '%id="pagination.*?</div>%usi',
                'base_pagination_link' => 'https://shop.villman.com/collections/all?',
                'pages_regex' => '%<a[^>]*?>(.*?)</a>%usi',
                'page_query' => 'page='
            ],
            'ajax' => [
                'api_base_url' => '',
                'product_link_base_url' => '',
            ],
            'container_regex' => '%product-list--collection.*?</div>.*class="pagination"%usi',
            'container_selector' => ''
        ]
    ];

    protected function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}
