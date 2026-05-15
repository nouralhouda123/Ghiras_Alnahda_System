<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IndicatorMatchingEngine;
use App\Services\KPIBrain;
use Illuminate\Support\Facades\DB;

class KPIController extends Controller
{
    public function step2(Request $request)
    {
        // STEP 1
        $kpi = (new KPIBrain())->analyze($request->kpi);

        // STEP 2 (NO AI)
        $engine = new IndicatorMatchingEngine(
            new \App\Repositories\IndicatorRepository()
        );

        $candidates = $engine->generateCandidates($kpi);

        return response()->json([
            'kpi' => $kpi,
            'candidates' => $candidates
        ]);
    }private function calculate(array $indicator, int $campaignId)
{
    return match ($indicator['operation']) {

        // =========================
        // 1. COUNT
        // =========================
        'count' => DB::table($indicator['table'])
            ->where('campaign_id', $campaignId)
            ->count(),

        // =========================
        // 2. SUM
        // =========================
        'sum' => DB::table($indicator['table'])
            ->where('campaign_id', $campaignId)
            ->sum($indicator['column']),

        // =========================
        // 3. AVERAGE
        // =========================
        'avg' => DB::table($indicator['table'])
            ->where('campaign_id', $campaignId)
            ->avg($indicator['column']),

        // =========================
        // 4. DISTINCT COUNT
        // =========================
        'distinct_count' => DB::table($indicator['table'])
            ->where('campaign_id', $campaignId)
            ->distinct()
            ->count($indicator['column']),

        // =========================
        // 5. GROWTH RATE
        // =========================
        'growth_rate' => $this->calculateGrowth($indicator, $campaignId),

        // =========================
        // 6. RATIO (مثل Attendance)
        // =========================
        'ratio' => $this->calculateRatio($indicator, $campaignId),

        default => null
    };
}private function calculateGrowth($indicator, $campaignId)
{
    $current = DB::table($indicator['table'])
        ->where('campaign_id', $campaignId)
        ->whereMonth('created_at', now()->month)
        ->count();

    $previous = DB::table($indicator['table'])
        ->where('campaign_id', $campaignId)
        ->whereMonth('created_at', now()->subMonth()->month)
        ->count();

    if ($previous == 0) return 0;

    return round((($current - $previous) / $previous) * 100, 2);
}private function calculateRatio($indicator, $campaignId)
{
    $total = DB::table('attendances')
        ->where('campaign_id', $campaignId)
        ->count();

    $present = DB::table('attendances')
        ->where('campaign_id', $campaignId)
        ->whereNotNull('check_in_time')
        ->count();

    if ($total == 0) return 0;

    return round(($present / $total) * 100, 2);
}
}
