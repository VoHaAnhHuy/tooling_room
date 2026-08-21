<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->string('ticket_id', 100)->primary();
            $table->string('ticket_type', 20)->notNull();
            $table->string('related_ticket_id', 100)->nullable();
            $table->string('product_id', 50)->nullable();
            $table->string('link_name', 100)->notNull();
            $table->string('requester_name', 100)->notNull();
            $table->string('issuer_name', 100)->notNull();
            $table->string('status', 20)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('related_ticket_id')
                ->references('ticket_id')
                ->on('tickets')
                ->onDelete('set null');

            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
