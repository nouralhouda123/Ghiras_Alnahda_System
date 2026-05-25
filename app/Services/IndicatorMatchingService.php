<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndicatorMatchingService
{
    /**
     */
    public function generate(array $aiResult, string $kpiText)
    {
        $domain = $result['domain'] ?? null;

        // 🟢 1. جلب مؤشرات من المكتبة حسب المجال
        $indicators = DB::table('indicators')
            ->where('domain', $domain)
            ->orderBy('priority', 'desc')
            ->get();

        // 🧠 تحويل KPI إلى Vector
        $kpiVector = $this->embed($kpiText);

        $results = [];

        foreach ($indicators as $indicator) {

            // 🧠 تحويل المؤشر إلى Vector
            $indicatorVector = $this->embed(
                $indicator->name . ' ' . ($indicator->description ?? '')
            );

            // 📊 2. حساب التشابه الدلالي
            $similarity = $this->cosineSimilarity($kpiVector, $indicatorVector);

            // 🗄️ 3. التحقق من توفر البيانات
            $dataAvailable = $this->checkDataAvailability($indicator);

            // 🧠 4. وزن التغذية الراجعة (لاحقًا)
            $feedbackWeight = $indicator->base_weight ?? 1;

            // ⚖️ 5. حساب السكور النهائي
            $score = $this->calculateScore(
                $similarity,
                $dataAvailable,
                $feedbackWeight
            );

            $results[] = [
                'id' => $indicator->id,
                'name' => $indicator->name,
                'domain' => $indicator->domain,
                'similarity' => round($similarity, 3),
                'data_available' => $dataAvailable,
                'score' => round($score, 3),
                'table' => $indicator->table_name,
                'operation' => $indicator->operation,
            ];
        }

        // 🏆 6. ترتيب النتائج
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    /**
     * 🧠 Embedding (Mock الآن — لاحقًا OpenAI)
     */
    private function embed(string $text): array
    {
        $text = strtolower(substr($text, 0, 50));

        $vector = [];

        for ($i = 0; $i < strlen($text); $i++) {
            $vector[] = ord($text[$i]) % 100;
        }

        return $vector;
    }

    /**
     * 📊 Cosine Similarity
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0;
        $magA = 0;
        $magB = 0;

        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $magA += $a[$i] ** 2;
            $magB += $b[$i] ** 2;
        }

        if ($magA == 0 || $magB == 0) return 0;

        return $dot / (sqrt($magA) * sqrt($magB));
    }

    /**
     * 🗄️ DB Validation
     */
    private function checkDataAvailability($indicator): bool
    {
        if (!Schema::hasTable($indicator->table_name)) {
            return false;
        }

        return DB::table($indicator->table_name)
            ->whereNotNull($indicator->column_name ?? 'id')
            ->exists();
    }


    private function calculateScore(
        float $similarity,
        bool $dataAvailable,
        float $feedbackWeight
    ): float {
        return (
            ($similarity * 0.5) +
            ($dataAvailable ? 0.3 : 0) +
            ($feedbackWeight * 0.2)
        );
    }
}
