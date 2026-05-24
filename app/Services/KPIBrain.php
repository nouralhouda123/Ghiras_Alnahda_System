<?php

namespace App\Services;


class KPIBrain
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 500;

    /**
     * 🔥 Synonyms normalization layer
     */
    private array $synonyms = [
        'زيادة' => 'increase',
        'رفع' => 'increase',
        'تحسين' => 'improve',
        'تطوير' => 'improve',
        'تقليل' => 'reduce',
        'خفض' => 'reduce',
        'متطوعين' => 'volunteers',
        'متطوع' => 'volunteers',
        'تبرع' => 'donation',
        'تبرعات' => 'donation',
    ];

    /**
     * 📌 Domain keywords
     */
    private array $domains = [
        'volunteers' => ['volunteer', 'participants', 'volunteers'],
        'donations'  => ['donation', 'funding', 'money'],
        'campaigns'  => ['campaign', 'initiative'],
    ];

    /**
     * 🧠 MAIN ENTRY
     */
    public function analyze(string $goal): array
    {
        $goal = trim($goal);

        $this->validate($goal);

        // 🔥 Step 1: Detect language
        $language = $this->detectLanguage($goal);

        // 🔥 Step 2: Normalize synonyms
        $normalized = $this->normalize($goal);

        // 🔥 Step 3: Fuzzy cleanup
        $cleanText = $this->fuzzyClean($normalized);

        // 🔥 Step 4: Analysis
        $domain = $this->detectDomain($cleanText);
        $intent = $this->detectIntent($cleanText);
        $target = $this->extractTarget($cleanText);

        $type = $target ? 'quantitative' : 'qualitative';

        $kpiType = $this->inferKpiType($intent, $type);

        $confidence = $this->calculateConfidence($domain, $intent, $target);

        // 🚨 Reject unclear goals
        if ($confidence < 0.3) {
            throw new \InvalidArgumentException("Goal is too unclear to analyze");
        }

        return [
            'original' => $goal,
            'cleaned' => $cleanText,
            'language' => $language,

            'domain' => $domain,
            'intent' => $intent,
            'type' => $type,
            'target' => $target,
            'kpi_type' => $kpiType,

            'confidence' => $confidence,
        ];
    }

    /**
     * 🌍 Detect language (Arabic / English)
     */
    private function detectLanguage(string $text): string
    {
        return preg_match('/\p{Arabic}/u', $text) ? 'ar' : 'en';
    }

    /**
     * 🔄 Normalize synonyms
     */
    private function normalize(string $text): string
    {
        return str_replace(
            array_keys($this->synonyms),
            array_values($this->synonyms),
            mb_strtolower($text)
        );
    }

    /**
     * 🧹 Fuzzy cleaning (noise removal)
     */
    private function fuzzyClean(string $text): string
    {
        // remove extra spaces + punctuation noise
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * 🧠 Domain detection
     */
    private function detectDomain(string $text): string
    {
        foreach ($this->domains as $domain => $keywords) {
            foreach ($keywords as $word) {
                if (str_contains($text, $word)) {
                    return $domain;
                }
            }
        }

        return 'general';
    }

    /**
     * 🎯 Intent inference (SMART)
     */
    private function detectIntent(string $text): string
    {
        if (str_contains($text, 'increase')) return 'growth';
        if (str_contains($text, 'improve')) return 'improvement';
        if (str_contains($text, 'reduce')) return 'reduction';

        return 'neutral';
    }

    /**
     * 🔢 Extract number (goal target)
     */
    private function extractTarget(string $text): ?int
    {
        if (preg_match('/\b\d+\b/', $text, $m)) {
            return (int) $m[0];
        }

        return null;
    }

    /**
     * 🧠 Smart KPI type inference
     */
    private function inferKpiType(string $intent, string $type): string
    {
        if ($intent === 'growth') {
            return 'lagging';
        }

        if ($type === 'qualitative') {
            return 'leading';
        }

        return 'lagging';
    }

    /**
     * 📊 Confidence scoring
     */
    private function calculateConfidence(string $domain, string $intent, ?int $target): float
    {
        $score = 0;

        if ($domain !== 'general') $score += 0.4;
        if ($intent !== 'neutral') $score += 0.3;
        if ($target !== null) $score += 0.3;

        return round($score, 2);
    }

    /**
     * 🛑 Validation layer
     */
    private function validate(string $goal): void
    {
        if (strlen($goal) < self::MIN_LENGTH) {
            throw new \InvalidArgumentException("Goal too short");
        }

        if (strlen($goal) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException("Goal too long");
        }

        // 🚨 reject numeric-only input
        if (preg_match('/^\d+$/', $goal)) {
            throw new \InvalidArgumentException("Goal cannot be only numbers");
        }
    }
}
