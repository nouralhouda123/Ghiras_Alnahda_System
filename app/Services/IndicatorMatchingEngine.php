<?php
namespace App\Services;

use App\Repositories\IndicatorRepository;
use Illuminate\Support\Facades\DB;

class IndicatorMatchingEngine
{
    public function __construct(
        protected IndicatorRepository $repo
    ) {}

    public function generateCandidates(array $kpi): array
    {
        // =========================
        // 1. Load Library Indicators
        // =========================
        $indicators = $this->repo->getByDomain($kpi['domain']);

        $candidates = [];

        foreach ($indicators as $indicator) {

            // =========================
            // 2. Rule-based filtering
            // =========================
            if (!$this->isRelevant($indicator, $kpi)) {
                continue;
            }

            // =========================
            // 3. Score calculation (NO AI)
            // =========================
            $score = $this->calculateScore($indicator, $kpi);

            $candidates[] = [
                'name' => $indicator->name,
                'domain' => $indicator->domain,
                'operation' => $indicator->operation,
                'table' => $indicator->table_name,
                'column' => $indicator->column_name,
                'score' => $score,
                'data_available' => $this->checkDataAvailability($indicator)
            ];
        }

        // =========================
        // 4. Ranking
        // =========================
        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);

        return $candidates;
    }

    // =========================
    // FILTER LOGIC
    // =========================
    private function isRelevant($indicator, $kpi): bool
    {
        // Domain must match
        if ($indicator->domain !== $kpi['domain']) {
            return false;
        }

        // Intent filtering
        if ($kpi['intent'] === 'growth') {

            return in_array($indicator->operation, [
                'count',
                'ratio',
                'growth_rate',
                'distinct_count'
            ]);
        }

        if ($kpi['intent'] === 'reduction') {

            return in_array($indicator->operation, [
                'drop_rate',
                'loss_rate'
            ]);
        }

        return true;
    }

    // =========================
    // SCORING ENGINE (NO AI)
    // =========================
    private function calculateScore($indicator, $kpi): float
    {
        $score = 0;

        // 1. Domain match (important)
        if ($indicator->domain === $kpi['domain']) {
            $score += 0.4;
        }

        // 2. Intent alignment
        if ($kpi['intent'] === 'growth') {

            if (str_contains($indicator->name, 'Total') ||
                str_contains($indicator->name, 'Active') ||
                str_contains($indicator->name, 'Growth')) {
                $score += 0.3;
            }
        }

        // 3. Target relevance
        if (!is_null($kpi['target'])) {

            if (str_contains($indicator->operation, 'count')) {
                $score += 0.2;
            }
        }

        // 4. Operation priority
        $score += match($indicator->operation) {
            'count' => 0.2,
            'ratio' => 0.15,
            'growth_rate' => 0.1,
            default => 0.05
        };

        return round($score, 2);
    }

    // =========================
    // DB CHECK (Data Availability)
    // =========================
    private function checkDataAvailability($indicator): bool
    {
        return DB::table($indicator->table_name)->exists();
    }
}
