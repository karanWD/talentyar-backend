<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('analyzer_status')->nullable()->after('status');
            $table->unsignedInteger('analyzer_attempts')->default(0)->after('analyzer_status');
            $table->unsignedInteger('analyzer_failure_count')->default(0)->after('analyzer_attempts');
            $table->unsignedInteger('analyzer_success_count')->default(0)->after('analyzer_failure_count');
            $table->text('analyzer_last_error')->nullable()->after('analyzer_success_count');
            $table->timestamp('analyzer_last_attempt_at')->nullable()->after('analyzer_last_error');
            $table->timestamp('analyzer_succeeded_at')->nullable()->after('analyzer_last_attempt_at');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'analyzer_status',
                'analyzer_attempts',
                'analyzer_failure_count',
                'analyzer_success_count',
                'analyzer_last_error',
                'analyzer_last_attempt_at',
                'analyzer_succeeded_at',
            ]);
        });
    }
};
