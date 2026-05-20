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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->double('price');
            $table->double('discount_price')->nullable();
            $table->integer('duration');
            $table->enum('type', \App\Models\Plan::TYPE)->default(\App\Models\Plan::TYPE_BASIC);
            $table->enum('status', \App\Models\Plan::STATUS)->default(\App\Models\Plan::STATUS_ACTIVE);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
