<?php

namespace App\Jobs;

use App\Models\Evaluation;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyzeEvaluationVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @param int $evaluationId
     */
    public function __construct(
        public int $evaluationId
    ) {}

    public function handle(): void
    {
        $evaluation = Evaluation::query()
            ->with(['user.activeSubscription.plan', 'media'])
            ->find($this->evaluationId);

        if (! $evaluation) {
            Log::warning('Evaluation analyze job skipped because evaluation was not found.', [
                'evaluation_id' => $this->evaluationId,
            ]);

            return;
        }

        $videoMedia = $evaluation->media->firstWhere('collection', 'video') ?? $evaluation->media->first();

        if (! $videoMedia) {
            Log::warning('Evaluation analyze job skipped because no video media was attached.', [
                'evaluation_id' => $evaluation->id,
            ]);

            return;
        }

        $payload = [
            'videoUrl' => $videoMedia->url,
            'userInfo' => [
                'age' => $this->resolveAge($evaluation->user?->birth_date),
                'gender' => $this->resolveGender($evaluation->user),
                'position' => $evaluation->user?->post_skill,
                'field' => 'football',
                'subscription' => $evaluation->user?->activeSubscription?->plan?->type ?? Plan::TYPE_BASIC,
            ],
        ];

        $response = Http::timeout(30)
            ->acceptJson()
            ->post(config('services.analyzer.url'), $payload);

        $response->throw();

        Log::info('Evaluation video sent to analyzer successfully.', [
            'evaluation_id' => $evaluation->id,
            'status' => $response->status(),
        ]);
    }

    private function resolveAge(?string $birthDate): ?int
    {
        if (! $birthDate) {
            return null;
        }

        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Throwable $exception) {
            Log::warning('Unable to calculate user age for evaluation analyzer payload.', [
                'birth_date' => $birthDate,
                'evaluation_id' => $this->evaluationId,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function resolveGender(?User $user): ?string
    {
        return match ($user?->gender) {
            User::GENDER_MALE => 'male',
            User::GENDER_FEMALE => 'female',
            default => null,
        };
    }
}
