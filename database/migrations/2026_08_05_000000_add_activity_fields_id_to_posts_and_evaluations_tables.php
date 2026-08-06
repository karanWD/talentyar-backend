<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('activity_fields_id')
                ->nullable()
                ->after('user_id')
                ->constrained('activity_fields')
                ->nullOnDelete();
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('activity_fields_id')
                ->nullable()
                ->after('user_id')
                ->constrained('activity_fields')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['activity_fields_id']);
            $table->dropColumn('activity_fields_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['activity_fields_id']);
            $table->dropColumn('activity_fields_id');
        });
    }
};
