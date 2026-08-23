<?php

namespace App\Jobs;

use App\Models\Evaluation;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use Throwable;

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
            ->with(['user', 'activityField', 'media'])
            ->find($this->evaluationId);

        if (! $evaluation) {
            Log::warning('Evaluation analyze job skipped because evaluation was not found.', [
                'evaluation_id' => $this->evaluationId,
            ]);

            return;
        }

        $evaluation->update([
            'analyzer_status' => Evaluation::ANALYZER_STATUS_PROCESSING,
            'analyzer_attempts' => $this->attempts(),
            'analyzer_last_attempt_at' => now(),
        ]);

        $videoMedia = $evaluation->media->firstWhere('collection', 'video') ?? $evaluation->media->first();

        if (! $videoMedia) {
            $evaluation->update([
                'analyzer_status' => Evaluation::ANALYZER_STATUS_SKIPPED,
                'analyzer_last_error' => 'No video media was attached to this evaluation.',
            ]);

            Log::warning('Evaluation analyze job skipped because no video media was attached.', [
                'evaluation_id' => $evaluation->id,
            ]);

            return;
        }

        $activeSubscription = Subscription::query()
            ->with('plan')
            ->where('user_id', $evaluation->user_id)
            ->where('activity_field_id', $evaluation->activity_fields_id)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('started_at', '<=', now())
            ->where('expires_at', '>', now())
            ->latest('started_at')
            ->first();

        $payload = [
            'evaluation_id' => $evaluation->id,
            'videoUrl' => $videoMedia->url,
            'userInfo' => [
                'age' => $this->resolveAge($evaluation->user?->birth_date),
                'gender' => $this->resolveGender($evaluation->user),
                'position' => $evaluation->user?->post_skill,
                'field' => $evaluation->activityField?->slug,
                'subscription' => $activeSubscription?->plan?->type ?? Plan::TYPE_BASIC,
            ],
        ];

        Log::warning('PAYLOAD-AI',$payload);

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post(config('services.analyzer.url'), $payload);

            $response->throw();

            $evaluation->increment('analyzer_success_count');
            $evaluation->update([
                'analyzer_status' => Evaluation::ANALYZER_STATUS_SUCCEEDED,
                'analyzer_last_error' => null,
                'analyzer_succeeded_at' => now(),
            ]);

            Log::info('Evaluation video sent to analyzer successfully.', [
                'evaluation_id' => $evaluation->id,
                'status' => $response->status(),
            ]);

            Log::info('Evaluation Ai Response', [
                'evaluation_id' => $evaluation->id,
                'status' => $response->body(),
            ]);

        } catch (Throwable $exception) {
            $evaluation->increment('analyzer_failure_count');
            $evaluation->update([
                'analyzer_status' => $this->attempts() >= $this->tries
                    ? Evaluation::ANALYZER_STATUS_FAILED
                    : Evaluation::ANALYZER_STATUS_RETRYING,
                'analyzer_last_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function resolveAge(?string $birthDate): ?int
    {
        if (! $birthDate) {
            return null;
        }

        try {
            return Jalalian::fromFormat('Y-m-d', $birthDate)->toCarbon()->age;
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
