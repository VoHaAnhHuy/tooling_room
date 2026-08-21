<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_collaterals', function (Blueprint $table) {
            $table->string('product_id', 50)->notNull();
            $table->string('collateral_id', 50)->notNull();

            $table->primary(['product_id', 'collateral_id']);

            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('collateral_id')
                ->references('collateral_id')
                ->on('collaterals')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_collaterals');
    }
};
