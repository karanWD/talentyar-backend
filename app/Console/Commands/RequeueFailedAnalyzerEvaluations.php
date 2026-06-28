<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeEvaluationVideo;
use App\Models\Evaluation;
use Illuminate\Console\Command;

class RequeueFailedAnalyzerEvaluations extends Command
{
    protected $signature = 'evaluations:requeue-analyzer-failed {--limit=100 : Maximum number of failed evaluations to requeue}';

    protected $description = 'Dispatch analyzer jobs for evaluations whose analyzer status is failed';

    public function handle(): int
    {
        $limit = max(0, (int) $this->option('limit'));

        $query = Evaluation::query()
            ->where('analyzer_status', Evaluation::ANALYZER_STATUS_FAILED)
            ->orderBy('id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $evaluations = $query->get();

        if ($evaluations->isEmpty()) {
            $this->info('No evaluations found with analyzer_status=failed.');

            return self::SUCCESS;
        }

        foreach ($evaluations as $evaluation) {
            AnalyzeEvaluationVideo::dispatch($evaluation->id);
            $this->line("Queued evaluation {$evaluation->id}");
        }

        $this->info("Dispatched {$evaluations->count()} analyzer job(s).");

        return self::SUCCESS;
    }
}
