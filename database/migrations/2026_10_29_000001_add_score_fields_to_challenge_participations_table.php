<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenge_participations', function (Blueprint $table) {
            $table->unsignedTinyInteger('score')->nullable()->after('user_id');
            $table->foreignId('scored_by_admin_id')
                ->nullable()
                ->after('score')
                ->constrained('admins')
                ->nullOnDelete();
            $table->timestamp('scored_at')->nullable()->after('scored_by_admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('challenge_participations', function (Blueprint $table) {
            $table->dropForeign(['scored_by_admin_id']);
            $table->dropColumn(['score', 'scored_by_admin_id', 'scored_at']);
        });
    }
};
