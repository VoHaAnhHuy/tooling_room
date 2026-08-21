<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 50)->primary();
            $table->string('product_name', 100)->notNull();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
