<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinet_slots', function (Blueprint $table) {
            $table->string('slot_id', 50)->primary();
            $table->string('cabinet_id', 50)->notNull();
            $table->integer('row_index')->notNull();
            $table->integer('column_index')->notNull();

            $table->foreign('cabinet_id')
                ->references('cabinet_id')
                ->on('cabinets')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabinet_slots');
    }
};
