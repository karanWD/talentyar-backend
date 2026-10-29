<?php

use App\Enums\ChallengeActionType;
use App\Enums\ChallengeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->enum('type', ChallengeType::values())
                ->default(ChallengeType::Score->value)
                ->after('prize');
            $table->enum('action_type', ChallengeActionType::values())
                ->nullable()
                ->after('type');
            $table->unsignedInteger('action_target_count')
                ->nullable()
                ->after('action_type');

            $table->index(['type', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->dropIndex(['type', 'action_type']);
            $table->dropColumn(['type', 'action_type', 'action_target_count']);
        });
    }
};
