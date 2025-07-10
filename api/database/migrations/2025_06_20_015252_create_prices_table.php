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
        Schema::create('prices', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('brand_id')->nullable()->constrained('brands', 'node_id')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->nullOnDelete();
            $table->foreignId('node_id')->nullable()->constrained('nodes', 'node_id')->nullOnDelete();
            $table->string('price_name');
            $table->foreignId('store_id')->constrained('stores', 'store_id');
            $table->string('price_store_ident');
            $table->char('currency', 3);
            $table->decimal('price', 16);
            $table->string('stock_status')->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->string('rating')->nullable();
            $table->string('price_url', 500);
            $table->timestamps();

            $table->index(['brand_id', 'node_id']);
            $table->index(['category_id', 'node_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
