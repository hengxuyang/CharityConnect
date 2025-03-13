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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('request_id')->nullable();
            $table->foreign('request_id')->references('id')->on('requests');
            $table->uuid('donation_id')->nullable();
            $table->foreign('donation_id')->references('id')->on('donations');
            $table->uuid('charity_id')->nullable();
            $table->foreign('charity_id')->references('id')->on('charities');
            $table->enum('type', ['request_update', 'donation_update', 'inventory_alert', 'thank_you_note']);
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
