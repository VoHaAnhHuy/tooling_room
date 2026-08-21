<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collateral_types', function (Blueprint $table) {
            $table->integer('type_id')->autoIncrement();
            $table->string('type_name', 100)->notNull();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collateral_types');
    }
};
