<?php
namespace App\Services;

class KPIEvaluationEngine
{
    public function evaluate(array $kpi, array $metrics): array
    {
        return [
            'achievement' => $this->calculateAchievement($kpi, $metrics),
            'status' => null,
            'gap' => null,
            'score' => null
        ];
    }

private function calculateAchievement(array $kpi, array $metrics): float
{
    $target = $kpi['target'] ?? 0;

    if ($target == 0) {
        return 0;
    }

    $actual = $this->getActualValue($metrics);

    return round(($actual / $target) * 100, 2);
}private function getActualValue(array $metrics): float
{
    $total = 0;

    foreach ($metrics as $metric) {
        $total += $metric['value'];
    }

    return $total;
}private function getStatus(float $achievement): string
{
    return match (true) {

        $achievement >= 100 => 'achieved',

        $achievement >= 80 => 'near_achieved',

        $achievement >= 50 => 'at_risk',

        default => 'failed'
    };
}private function calculateGap(array $kpi, array $metrics): float
{
    $target = $kpi['target'] ?? 0;
    $actual = $this->getActualValue($metrics);

    return max($target - $actual, 0);
}public function evaluate(array $kpi, array $metrics): array
{
    $achievement = $this->calculateAchievement($kpi, $metrics);

    return [
        'achievement' => $achievement,
        'status' => $this->getStatus($achievement),
        'gap' => $this->calculateGap($kpi, $metrics),
        'target' => $kpi['target'],
        'actual' => $this->getActualValue($metrics)
    ];
}}
