<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaterals', function (Blueprint $table) {
            $table->string('collateral_id', 50)->primary();
            $table->integer('type_id')->notNull();
            $table->string('name', 150)->notNull();
            $table->string('location_status', 20)->nullable();
            $table->string('current_slot_id', 50)->nullable();
            $table->string('status', 20)->nullable();

            $table->foreign('type_id')
                ->references('type_id')
                ->on('collateral_types')
                ->onDelete('restrict');

            $table->foreign('current_slot_id')
                ->references('slot_id')
                ->on('cabinet_slots')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaterals');
    }
};
