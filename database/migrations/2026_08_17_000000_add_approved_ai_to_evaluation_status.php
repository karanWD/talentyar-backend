<?php

use App\Models\Evaluation;
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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('status', Evaluation::STATUSES)
                ->default(Evaluation::status_pending)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('evaluations')
            ->where('status', Evaluation::status_approved_ai)
            ->update(['status' => Evaluation::status_approved]);

        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('status', [
                Evaluation::status_pending,
                Evaluation::status_approved,
                Evaluation::status_rejected,
            ])->default(Evaluation::status_pending)->change();
        });
    }
};
