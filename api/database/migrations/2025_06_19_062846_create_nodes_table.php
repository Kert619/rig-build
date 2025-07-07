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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id('node_id');
            $table->string('node_name');
            $table->string('node_regexp');
            $table->string('node_not_regexp')->nullable();
            $table->foreignId('node_type_id')->constrained('node_types', 'node_type_id');
            $table->foreignId('parent_node_id')->nullable()->constrained('nodes', 'node_id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
