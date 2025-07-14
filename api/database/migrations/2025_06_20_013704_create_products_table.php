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
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('node_id')->primary();
            $table->string('product_name');
            $table->foreignId('brand_id')->constrained('brands', 'node_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->foreign('node_id')->references('node_id')->on('nodes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
