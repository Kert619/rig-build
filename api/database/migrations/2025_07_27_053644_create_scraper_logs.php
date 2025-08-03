<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scraper_logs', function (Blueprint $table) {
            $table->id('scraper_log_id');
            $table->foreignId('scraper_id')->constrained('scrapers', 'scraper_id');
            $table->timestamp('started')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('ended')->nullable()->default(null);
            $table->text('error_message')->nullable();
            $table->integer('price_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraper_logs');
    }
};
