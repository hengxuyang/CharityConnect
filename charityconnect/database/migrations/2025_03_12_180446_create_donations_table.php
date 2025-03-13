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
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('donor_id');
            $table->foreign('donor_id')->references('id')->on('users');
            $table->uuid('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->integer('quantity');
            $table->enum('status', ['Pending', 'Delivered', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
