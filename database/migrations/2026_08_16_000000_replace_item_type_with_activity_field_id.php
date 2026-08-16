<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        $hasTypeColumn = Schema::hasColumn('items', 'type');
        $hasActivityFieldIdColumn = Schema::hasColumn('items', 'activity_field_id');

        if ($hasTypeColumn) {
            if (! $hasActivityFieldIdColumn) {
                Schema::table('items', function (Blueprint $table) {
                    $table->unsignedBigInteger('activity_field_id')->nullable()->after('name');
                });
            }

            DB::table('items')
                ->whereNull('activity_field_id')
                ->update(['activity_field_id' => 1]);

            Schema::table('items', function (Blueprint $table) {
                $table->dropUnique('items_name_type_unique');
            });

            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('type');
            });

            Schema::table('items', function (Blueprint $table) {
                $table->unique(['name', 'activity_field_id']);
            });
        } elseif ($hasActivityFieldIdColumn) {
            DB::table('items')
                ->whereNull('activity_field_id')
                ->update(['activity_field_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('items') || Schema::hasColumn('items', 'type')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
        });

        DB::table('items')->update(['type' => 'football']);

        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('items_name_activity_field_id_unique');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('activity_field_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unique(['name', 'type']);
        });
    }
};
