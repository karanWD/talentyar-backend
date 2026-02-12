<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostCommentLikeSeeder extends Seeder
{
    public const POST_COUNT = 300;
    public const COMMENT_COUNT = 1000;
    public const LIKE_COUNT = 3000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();
        if (count($userIds) < 2) {
            $this->command->warn('Need at least 2 users. Run UserSeeder first.');
            return;
        }

        $this->command->info('Creating media and posts...');
        $mediaIds = $this->createMediaAndPosts($userIds);

        $postIds = Post::query()->pluck('id')->all();
        if (empty($postIds)) {
            return;
        }

        $this->command->info('Creating comments...');
        $this->createComments($userIds, $postIds);

        $this->command->info('Creating likes and dislikes...');
        $this->createLikes($userIds, $postIds);

        $this->command->info('Post, comment and like seeding done.');
    }

    /**
     * @param array<int> $userIds
     * @return array<int>
     */
    private function createMediaAndPosts(array $userIds): array
    {
        $now = now();
        $hashes = [];
        $insertMediaChunk = [];
        $perChunk = 100;

        for ($i = 1; $i <= self::POST_COUNT; $i++) {
            $hash = hash('sha256', 'seed-video-' . $i . '-' . uniqid('', true));
            $hashes[] = $hash;
            $path = sprintf(
                'media/user/%s/%s/%s.mp4',
                substr($hash, 0, 2),
                substr($hash, 2, 2),
                $hash
            );
            $insertMediaChunk[] = [
                'path' => $path,
                'hash' => $hash,
                'name' => 'seed-video-' . $i,
                'entity_slug' => 'user',
                'collection' => 'default',
                'extension' => 'mp4',
                'mime_type' => 'video/mp4',
                'size' => (string) fake()->numberBetween(1000000, 50000000),
                'type' => Media::TYPE_FILE,
                'disk' => 'public',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($insertMediaChunk) >= $perChunk) {
                DB::table('media')->insert($insertMediaChunk);
                $insertMediaChunk = [];
            }
        }
        if ($insertMediaChunk !== []) {
            DB::table('media')->insert($insertMediaChunk);
        }

        $mediaRecords = Media::query()->whereIn('hash', $hashes)->orderBy('id')->get();
        if ($mediaRecords->isEmpty()) {
            $this->command->warn('No media created.');
            return [];
        }

        $postInserts = [];
        $now = now();
        foreach ($mediaRecords as $media) {
            $postInserts[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'state' => fake()->randomElement([Post::STATE_PUBLISHED, Post::STATE_PUBLISHED, Post::STATE_DRAFT]),
                'caption' => fake()->sentence(8),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($postInserts, 100) as $chunk) {
            DB::table('posts')->insert($chunk);
        }

        $newPostIds = Post::query()->orderByDesc('id')->limit(count($postInserts))->pluck('id')->all();
        $newPostIds = array_reverse($newPostIds);
        foreach ($mediaRecords->values() as $i => $media) {
            $postId = $newPostIds[$i] ?? null;
            if ($postId) {
                $media->update(['entity_type' => Post::class, 'entity_id' => $postId]);
            }
        }

        return $mediaRecords->pluck('id')->all();
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $postIds
     */
    private function createComments(array $userIds, array $postIds): void
    {
        $now = now();
        $chunk = [];
        $insertChunkSize = 200;

        for ($i = 0; $i < self::COMMENT_COUNT; $i++) {
            $chunk[] = [
                'post_id' => $postIds[array_rand($postIds)],
                'user_id' => $userIds[array_rand($userIds)],
                'body' => fake()->sentence(rand(3, 12)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (count($chunk) >= $insertChunkSize) {
                DB::table('comments')->insert($chunk);
                $chunk = [];
            }
        }
        if ($chunk !== []) {
            DB::table('comments')->insert($chunk);
        }
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $postIds
     */
    private function createLikes(array $userIds, array $postIds): void
    {
        $now = now();
        $seen = [];
        $chunk = [];
        $insertChunkSize = 500;

        for ($i = 0; $i < self::LIKE_COUNT; $i++) {
            $postId = $postIds[array_rand($postIds)];
            $userId = $userIds[array_rand($userIds)];
            $key = $postId . '_' . $userId;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $chunk[] = [
                'post_id' => $postId,
                'user_id' => $userId,
                'type' => fake()->randomElement([PostLike::TYPE_LIKE, PostLike::TYPE_DISLIKE]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (count($chunk) >= $insertChunkSize) {
                DB::table('post_likes')->insert($chunk);
                $chunk = [];
            }
        }
        if ($chunk !== []) {
            DB::table('post_likes')->insert($chunk);
        }
    }
}
