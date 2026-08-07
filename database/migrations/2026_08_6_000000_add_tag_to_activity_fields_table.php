<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_fields', function (Blueprint $table) {
            $table->string('tag')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('activity_fields', function (Blueprint $table) {
            $table->dropColumn(['tag']);
        });
    }
};
