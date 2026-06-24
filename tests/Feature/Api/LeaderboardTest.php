<?php

use App\Models\Evaluation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('leaderboard ranks users by approved evaluation score', function () {
    $authUser = User::factory()->create();
    Sanctum::actingAs($authUser);

    $topUser = User::factory()->create(['username' => 'top-player']);
    $runnerUp = User::factory()->create(['username' => 'runner-up']);
    $thirdUser = User::factory()->create(['username' => 'third-player']);
    $pendingOnlyUser = User::factory()->create(['username' => 'pending-only']);

    Evaluation::create([
        'user_id' => $topUser->id,
        'score' => 95,
        'status' => Evaluation::status_approved,
    ]);

    Evaluation::create([
        'user_id' => $runnerUp->id,
        'score' => 90,
        'status' => Evaluation::status_approved,
    ]);

    Evaluation::create([
        'user_id' => $runnerUp->id,
        'score' => 80,
        'status' => Evaluation::status_approved,
    ]);

    Evaluation::create([
        'user_id' => $thirdUser->id,
        'score' => 70,
        'status' => Evaluation::status_approved,
    ]);

    Evaluation::create([
        'user_id' => $pendingOnlyUser->id,
        'score' => 100,
        'status' => Evaluation::status_pending,
    ]);

    $response = $this->getJson('/api/v1/user/leaderboard?per_page=10');

    $response->assertOk();
    $response->assertJsonPath('data.users.0.username', 'top-player');
    $response->assertJsonPath('data.users.0.leaderboard_score', 95);
    $response->assertJsonPath('data.users.1.username', 'runner-up');
    $response->assertJsonPath('data.users.1.leaderboard_score', 85);
    $response->assertJsonPath('data.users.1.approved_evaluations_count', 2);
    $response->assertJsonPath('data.users.2.username', 'third-player');

    expect(collect($response->json('data.users'))->pluck('username')->all())
        ->not->toContain('pending-only');
});
