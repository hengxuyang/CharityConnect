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
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('charity_id');
            $table->foreign('charity_id')->references('id')->on('charities');
            $table->uuid('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('quantity');
            $table->enum('request_type', ['drop-off', 'pickup'])->default('drop-off');
            $table->text('drop_off_address');
            $table->dateTime('available_times');
            $table->enum('status', ['Pending', 'Fulfilled', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
