<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_field_id')
                ->constrained('activity_fields')
                ->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('about');
            $table->text('conditions');
            $table->text('rules');
            $table->string('prize');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->unsignedInteger('active_duration_days');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['activity_field_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
