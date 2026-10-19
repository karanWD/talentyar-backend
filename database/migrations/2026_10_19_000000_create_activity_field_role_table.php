<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_field_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['activity_field_id', 'role_id']);
        });

        // Existing roles were unrestricted. Associate them with every current
        // activity field so deploying this migration does not silently revoke access.
        $roleIds = DB::table('roles')->pluck('id');
        $activityFieldIds = DB::table('activity_fields')->pluck('id');
        $timestamp = now();

        foreach ($roleIds as $roleId) {
            $rows = $activityFieldIds->map(fn ($activityFieldId): array => [
                'activity_field_id' => $activityFieldId,
                'role_id' => $roleId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all();

            if ($rows !== []) {
                DB::table('activity_field_role')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_field_role');
    }
};
