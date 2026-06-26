<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = [
                [
                    'first_name' => 'Ali',
                    'last_name' => 'Sadr',
                    'phone' => '09111111111',
                    'email' => 'leaderboard.top@example.com',
                    'username' => 'top-player',
                    'scores' => [96],
                ],
                [
                    'first_name' => 'Reza',
                    'last_name' => 'Karimi',
                    'phone' => '09222222222',
                    'email' => 'leaderboard.runnerup@example.com',
                    'username' => 'runner-up',
                    'scores' => [90, 80],
                ],
                [
                    'first_name' => 'Pouya',
                    'last_name' => 'Ahmadi',
                    'phone' => '09333333333',
                    'email' => 'leaderboard.third@example.com',
                    'username' => 'third-player',
                    'scores' => [70],
                ],
                [
                    'first_name' => 'Nima',
                    'last_name' => 'Taheri',
                    'phone' => '09444444444',
                    'email' => 'leaderboard.tie1@example.com',
                    'username' => 'tie-break-higher-count',
                    'scores' => [85, 85, 85],
                ],
                [
                    'first_name' => 'Sina',
                    'last_name' => 'Jafari',
                    'phone' => '09555555555',
                    'email' => 'leaderboard.tie2@example.com',
                    'username' => 'tie-break-lower-count',
                    'scores' => [85],
                ],
                [
                    'first_name' => 'Arman',
                    'last_name' => 'Moradi',
                    'phone' => '09666666666',
                    'email' => 'leaderboard.pending@example.com',
                    'username' => 'pending-only',
                    'scores' => [100],
                    'approved' => false,
                ],
            ];

            foreach ($users as $data) {
                $user = User::updateOrCreate(
                    ['username' => $data['username']],
                    [
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'email' => $data['email'],
                        'gender' => User::GENDER_MALE,
                        'foot_specialization' => User::FOOT_SPECIALIZATION_RIGHT,
                        'post_skill' => User::POST_SKILL_CF,
                        'skill_level' => User::SKILL_LEVEL_PROFESSIONAL,
                        'activity_history' => true,
                        'birth_date' => '2004-01-01',
                        'weight' => 75,
                        'height' => 180,
                        'bio' => 'Seeded leaderboard player.',
                    ]
                );

                Evaluation::query()->where('user_id', $user->id)->delete();

                foreach ($data['scores'] as $score) {
                    Evaluation::query()->create([
                        'user_id' => $user->id,
                        'score' => $score,
                        'status' => ($data['approved'] ?? true)
                            ? Evaluation::status_approved
                            : Evaluation::status_pending,
                    ]);
                }
            }
        });
    }
}
