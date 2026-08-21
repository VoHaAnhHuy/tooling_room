<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinets', function (Blueprint $table) {
            $table->string('cabinet_id', 50)->primary();
            $table->string('cabinet_name', 100)->notNull();
            $table->integer('total_rows')->notNull();
            $table->integer('total_columns')->notNull();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabinets');
    }
};
