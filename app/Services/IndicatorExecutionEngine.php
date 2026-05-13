<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class IndicatorExecutionEngine
{
    public function execute(array $indicators, int $campaignId): array
    {
        $results = [];

        foreach ($indicators as $indicator) {

            $results[] = [
                'name' => $indicator['name'],
                'value' => $this->resolve($indicator, $campaignId)
            ];
        }

        return $results;
    }

    private function resolve(array $indicator, int $campaignId)
    {
        return match ($indicator['operation']) {

            'count' => DB::table($indicator['table'])
                ->where('campaign_id', $campaignId)
                ->count(),

            'sum' => DB::table($indicator['table'])
                ->where('campaign_id', $campaignId)
                ->sum($indicator['column']),

            'avg' => DB::table($indicator['table'])
                ->where('campaign_id', $campaignId)
                ->avg($indicator['column']),

            'ratio' => $this->calculateRatio($campaignId),

            default => null
        };
    }

    private function calculateRatio($campaignId)
    {
        $total = DB::table('attendances')
            ->where('campaign_id', $campaignId)
            ->count();

        $present = DB::table('attendances')
            ->where('campaign_id', $campaignId)
            ->whereNotNull('check_in_time')
            ->count();

        return $total == 0 ? 0 : ($present / $total) * 100;
    }
}
