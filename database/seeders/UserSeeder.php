<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public const USER_COUNT = 1000;

    public const CHUNK_SIZE = 200;

    /**
     * Minimum and maximum number of users each user follows (random in this range).
     */
    public const FOLLOWS_MIN = 50;

    public const FOLLOWS_MAX = 500;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating ' . self::USER_COUNT . ' users...');

        $created = 0;
        while ($created < self::USER_COUNT) {
            $chunkSize = min(self::CHUNK_SIZE, self::USER_COUNT - $created);
            User::factory()
                ->count($chunkSize)
                ->sequence(function ($seq) use ($created) {
                    $i = $created + $seq->index + 1;
                    return [
                        'phone' => '09' . str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                        'username' => 'user_' . $i,
                        'email' => 'user' . $i . '@example.com',
                    ];
                })
                ->create();
            $created += $chunkSize;
            $this->command->info("Created {$created}/" . self::USER_COUNT . ' users.');
        }

        $this->command->info('Creating random follow relationships...');

        $userIds = User::query()->orderBy('id')->pluck('id')->all();
        $total = count($userIds);
        $now = now();
        $insertChunkSize = 2000;
        $chunk = [];

        foreach ($userIds as $followerId) {
            $count = random_int(self::FOLLOWS_MIN, min(self::FOLLOWS_MAX, $total - 1));
            $candidates = array_values(array_diff($userIds, [$followerId]));
            shuffle($candidates);
            $picks = array_slice($candidates, 0, $count);

            foreach ($picks as $followingId) {
                $chunk[] = [
                    'follower_id' => $followerId,
                    'following_id' => $followingId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($chunk) >= $insertChunkSize) {
                    DB::table('user_follows')->insert($chunk);
                    $chunk = [];
                }
            }
        }

        if ($chunk !== []) {
            DB::table('user_follows')->insert($chunk);
        }

        $this->command->info('Follow relationships created.');
    }
}
