<?php

namespace App\Services;

class KPIBrain
{
    public function analyze(string $text): array
    {
        $text = trim($text);

        return [
            "domain" => $this->detectDomain($text),
            "intent" => $this->detectIntent($text),
            "target" => $this->detectTarget($text),
            "timeframe" => $this->detectTimeframe($text),
            "confidence" => $this->calculateConfidence($text)
        ];
    }

    private function detectDomain(string $text): ?string
    {
        $domains = [

            "volunteers" => [
                "متطوع",
                "متطوعين",
                "تطوع"
            ],

            "donations" => [
                "تبرع",
                "تبرعات",
                "تمويل"
            ],

            "beneficiaries" => [
                "مستفيد",
                "مستفيدين"
            ]
        ];

        foreach ($domains as $domain => $keywords) {

            foreach ($keywords as $keyword) {

                if (mb_stripos($text, $keyword) !== false) {
                    return $domain;
                }
            }
        }

        return null;
    }

    private function detectIntent(string $text): string
    {
        $growthWords = [
            "زيادة",
            "رفع",
            "تحسين",
            "تطوير",
            "مضاعفة"
        ];

        $reduceWords = [
            "تقليل",
            "خفض"
        ];

        foreach ($growthWords as $word) {

            if (mb_stripos($text, $word) !== false) {
                return "growth";
            }
        }

        foreach ($reduceWords as $word) {

            if (mb_stripos($text, $word) !== false) {
                return "reduction";
            }
        }

        return "unknown";
    }

    private function detectTarget(string $text): ?int
    {
        preg_match('/\d+/', $text, $matches);

        return isset($matches[0])
            ? (int)$matches[0]
            : null;
    }

    private function detectTimeframe(string $text): ?string
    {
        preg_match('/20\d{2}/', $text, $matches);

        return $matches[0] ?? null;
    }

    private function calculateConfidence(string $text): float
    {
        $score = 0;

        if ($this->detectDomain($text)) {
            $score += 0.25;
        }

        if ($this->detectIntent($text) !== "unknown") {
            $score += 0.25;
        }

        if ($this->detectTarget($text)) {
            $score += 0.25;
        }

        if ($this->detectTimeframe($text)) {
            $score += 0.25;
        }

        return $score;
    }
}
