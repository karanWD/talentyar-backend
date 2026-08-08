<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropUnique('plans_name_unique');
            $table->foreignId('activity_field_id')
                ->nullable()
                ->after('id')
                ->constrained('activity_fields')
                ->restrictOnDelete();
            $table->unique(['activity_field_id', 'name']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('activity_field_id')
                ->nullable()
                ->after('user_id')
                ->constrained('activity_fields')
                ->restrictOnDelete();
            $table->index(
                ['user_id', 'activity_field_id', 'status', 'expires_at'],
                'subscriptions_user_activity_status_expires_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_user_activity_status_expires_index');
            $table->dropConstrainedForeignId('activity_field_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropUnique(['activity_field_id', 'name']);
            $table->dropConstrainedForeignId('activity_field_id');
            $table->unique('name');
        });
    }
};
