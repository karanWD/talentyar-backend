<?php

namespace App\Http\Controllers\Api;

use App\Jobs\AnalyzeEvaluationVideo;
use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\MediaResource;
use App\Models\Evaluation;
use App\Models\Media;
use App\Models\Post;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class EvaluationController extends BaseApiController
{
    private const JALALI_MONTHS = [
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    ];

    public function months(Request $request): JsonResponse
    {
        $now = Jalalian::now();
        $year = $request->integer('year', $now->getYear());

        if ($year < 1000 || $year > 3000) {
            return $this->errorResponse('Invalid Jalali year', 422);
        }

        $startDate = (new Jalalian($year, 1, 1))->toCarbon()->startOfDay();
        $endDate = (new Jalalian($year + 1, 1, 1))->toCarbon()->startOfDay();

        $evaluations = $request->user()
            ->evaluations()
            ->with(['media', 'items.item'])
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->latest()
            ->get()
            ->groupBy(fn ($evaluation) => Jalalian::fromCarbon($evaluation->created_at)->getMonth());

        $lastVisibleMonth = match (true) {
            $year < $now->getYear() => 12,
            $year === $now->getYear() => $now->getMonth(),
            default => 0,
        };

        $months = collect(self::JALALI_MONTHS)
            ->filter(fn (string $name, int $month) => $month <= $lastVisibleMonth)
            ->map(fn (string $name, int $month) => [
            'month' => $month,
            'name' => $name,
            'has_evaluation' => $evaluations->has($month),
            'can_upload' => $year === $now->getYear()
                && $month === $now->getMonth()
                && ! $evaluations->has($month),
            'evaluation' => $evaluations->has($month)
                ? new EvaluationResource($evaluations->get($month)->first())
                : null,
            ])
            ->values();

        return $this->successResponse([
            'year' => $year,
            'months' => $months,
        ], 'Evaluation months retrieved successfully');
    }

    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $mediaService = new MediaService();

        $media = $mediaService->addMediaFromUploadedFile(
            $file,
            'user',
            $request->validated('type', Media::TYPE_EVALUATION),
            'default',
            's3',
            $request->only(['alt'])
        );

        return $this->successResponse(
            [
                'media' => new MediaResource($media),
                'hash' => $media->hash,
            ],
            'Media uploaded successfully',
            201
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'video_hash' => ['required', 'string', 'exists:media,hash'],
        ]);

        $user = $request->user();

        $evaluation = Evaluation::create([
            'user_id' => $user->id,
            'analyzer_status' => Evaluation::ANALYZER_STATUS_PENDING,
        ]);

        $videoMedia = Media::where('hash', $validated['video_hash'])->firstOrFail();
        $evaluation->assignMedia($videoMedia, 'video');


        $post = Post::create([
            'user_id' => $user->id,
            'caption' => 'Evaluation',
            'state' => Post::STATE_DRAFT,
        ]);

        $postMedia = $videoMedia->replicate([
            'entity_id',
            'entity_type',
        ]);

        $postMedia->entity_id = $post->id;
        $postMedia->entity_type = get_class($post);
        $postMedia->save();

        AnalyzeEvaluationVideo::dispatch($evaluation->id)->afterCommit();

        $evaluation->load('media');

        return $this->successResponse(
            ['post' => new EvaluationResource($evaluation)],
            'Evaluation created successfully',
            201
        );
    }
}
