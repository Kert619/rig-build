<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $default = json_encode([
            'settings' => 'puppeteer',
            'category' => [
                'container_extract_method' => 'regex',
                'container_regex' => '',
                'container_selector' => '',
                'regex' => '',
            ],
            'product' => [
                'method' => 'regex',
                'container_regex' => '',
                'regex' => '',
                'container_selector' => '',
                'selector' => '',
                'pagination' => [
                    'method' => 'regex',
                    'container_regex' => '',
                    'container_selector' => '',
                    'query_separator' => '',
                    'page_query' => '',
                    'pages_regex' => '',
                ],
                'ajax' => [
                    'api_base_url' => '',
                    'product_link_base_url' => '',
                ],
                'format' => [
                    'currency' => '',
                    'img_url' => '',
                    'price' => '',
                    'price_name' => '',
                    'price_store_ident' => '',
                    'price_url' => '',
                    'rating' => '',
                    'stock_quantity' => '',
                    'stock_status' => '',
                ],
                'page_rules' => [],
                'variant_flag' => [
                    'regex' => ''
                ]
            ],
        ]);

        Schema::create('scrapers', function (Blueprint $table) use ($default) {
            $table->id('scraper_id');
            $table->foreignId('store_id')->constrained('stores', 'store_id');
            $table->string('scraper_name');
            $table->string('scraper_url');
            $table->json('scraper_config')->default($default);
            $table->tinyInteger('is_running')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->timestamp('last_run')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapers');
    }
};
