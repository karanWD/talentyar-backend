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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('username')->unique()->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedTinyInteger('gender',\App\Models\User::GENDERS)->nullable();
            $table->string('birth_date')->nullable();
            $table->integer('weight')->default(0);
            $table->integer('height')->default(0);
            $table->enum('foot_specialization',\App\Models\User::FOOT_SPECIALIZATION)->nullable();
            $table->enum('post_skill',\App\Models\User::POST_SKILL)->nullable();
            $table->enum('skill_level',\App\Models\User::SKILL_LEVEL)->nullable();
            $table->boolean('activity_history')->nullable();
            $table->string('team_name')->nullable();
            $table->string('favorite_iranian_team')->nullable();
            $table->string('favorite_foreign_team')->nullable();
            $table->integer('shirt_number')->nullable();
            $table->string('bio')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
