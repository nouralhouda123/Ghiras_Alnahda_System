<?php

namespace App\Services;

class KPIBrain
{
    public function analyze(string $goal): array
    {
        $text = $this->normalize($goal);

        $domain = $this->detectDomain($text);
        $intent = $this->detectIntent($text);
        $type = $this->detectType($text, $domain);
        $target = $this->extractTarget($text);

        return [
            'domain' => $domain,
            'intent' => $intent,
            'type' => $type,
            'target_value' => $target,
        ];
    }

    // =====================================================
    // 🧹 CLEAN
    // =====================================================
    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s%]/u', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    // =====================================================
    // 🌍 DOMAIN (EXPANDED NGO MAP)
    // =====================================================
    private function detectDomain(string $text): string
    {
        $map = [

            'volunteers' => ['متطوع', 'متطوعين', 'volunteer', 'مشاركين', 'طلاب'],

            'attendance' => ['حضور', 'attendance', 'تواجد'],

            'donations' => ['تبرع', 'تبرعات', 'donation', 'تمويل', 'مال'],

            'quality' => ['رضا', 'satisfaction', 'جودة', 'تقييم', 'تجربة'],

            'training' => ['ورشة', 'تدريب', 'دورة', 'workshop'],

            'awareness' => ['وعي', 'توعية', 'تثقيف', 'نظافة'],

            'points' => ['نقاط', 'points'],

        ];

        foreach ($map as $domain => $keywords) {
            foreach ($keywords as $word) {
                if (str_contains($text, $word)) {
                    return $domain;
                }
            }
        }

        return 'general';
    }

    // =====================================================
    // 🎯 INTENT (FIXED LOGIC)
    // =====================================================
    private function detectIntent(string $text): string
    {
        // growth (زيادة / عدد / رفع)
        if ($this->hasAny($text, ['زيادة', 'رفع', 'increase', 'عدد'])) {
            return 'growth';
        }

        // improvement (رضا / تحسين / تحقيق / رفع مستوى)
        if ($this->hasAny($text, ['تحقيق', 'تحسين', 'رفع مستوى', 'رضا', 'improve'])) {
            return 'improvement';
        }

        if ($this->hasAny($text, ['تقليل', 'خفض', 'reduce'])) {
            return 'reduction';
        }

        return 'neutral';
    }

    // =====================================================
    // 📊 TYPE (IMPORTANT FIX for QUALITY DOMAIN)
    // =====================================================
    private function detectType(string $text, string $domain): string
    {
        // 🚨 critical rule
        if ($domain === 'quality' || $domain === 'awareness') {
            return 'qualitative';
        }

        if (preg_match('/\d+/', $text) || str_contains($text, '%')) {
            return 'quantitative';
        }

        return 'qualitative';
    }

    // =====================================================
    // 🔢 TARGET EXTRACTION
    // =====================================================
    private function extractTarget(string $text): ?int
    {
        if (preg_match('/(\d+)\s*%/', $text, $m)) {
            return (int) $m[1];
        }

        if (preg_match('/\b\d+\b/', $text, $m)) {
            return (int) $m[0];
        }

        return null;
    }

    // =====================================================
    // 🧠 HELPERS
    // =====================================================
    private function hasAny(string $text, array $words): bool
    {
        foreach ($words as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }
        return false;
    }
}
