<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_items', function (Blueprint $table) {
            $table->bigIncrements('item_id');
            $table->string('ticket_id', 50)->notNull();
            $table->string('collateral_id', 50)->notNull();
            $table->string('from_slot_id', 50)->nullable();
            $table->string('to_slot_id', 50)->nullable();
            $table->text('condition_description')->nullable();
            $table->string('image_url', 300)->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->foreign('ticket_id')
                ->references('ticket_id')
                ->on('tickets')
                ->onDelete('cascade');

            $table->foreign('collateral_id')
                ->references('collateral_id')
                ->on('collaterals')
                ->onDelete('cascade');

            $table->foreign('from_slot_id')
                ->references('slot_id')
                ->on('cabinet_slots')
                ->onDelete('set null');

            $table->foreign('to_slot_id')
                ->references('slot_id')
                ->on('cabinet_slots')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_items');
    }
};
