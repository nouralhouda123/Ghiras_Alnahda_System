<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KPIEngineService
{
    public function analyze(string $text): array
    {
        try {

            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(60)
                ->retry(2, 200)
                ->post('https://api.openai.com/v1/chat/completions', [
                    "model" => "gpt-4o-mini",
                    "temperature" => 0,
                    "response_format" => ["type" => "json_object"],
                    "messages" => [
                        [
                            "role" => "system",
                            "content" => $this->systemPrompt()
                        ],
                        [
                            "role" => "user",
                            "content" => $text
                        ]
                    ]
                ]);

            // 🔴 إذا الطلب فشل
            if (!$response->successful()) {
                return [
                    "success" => false,
                    "error" => "API_FAILED",
                    "status" => $response->status(),
                    "body" => $response->body()
                ];
            }

            $data = $response->json();

            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                return $this->fallback("EMPTY_CONTENT");
            }

            $parsed = json_decode($content, true);

            if (!is_array($parsed)) {
                return $this->fallback("INVALID_JSON", $content);
            }

            return [
                "success" => true,
                "domain" => $parsed["domain"] ?? null,
                "intent" => $parsed["intent"] ?? "unknown",
                "target" => $parsed["target"] ?? null,
                "timeframe" => $parsed["timeframe"] ?? "unknown",
                "confidence" => 1.0,
                "source" => "ai"
            ];

        } catch (\Exception $e) {
            return [
                "success" => false,
                "error" => "EXCEPTION",
                "message" => $e->getMessage()
            ];
        }
    }

    private function fallback($reason, $raw = null): array
    {
        return [
            "success" => false,
            "domain" => null,
            "intent" => "unknown",
            "target" => null,
            "timeframe" => "unknown",
            "error" => $reason,
            "raw" => $raw
        ];
    }

    private function systemPrompt(): string
    {
        return <<<TXT
You are a KPI extraction engine.

Return ONLY valid JSON.

Format:
{
  "domain": string or null,
  "intent": "growth" | "reduce" | "maintain" | "unknown",
  "target": number or null,
  "timeframe": "week" | "month" | "year" | "unknown"
}

Rules:
- NO explanation
- NO markdown
- JSON ONLY
- Extract meaning from Arabic or English text
TXT;
    }
}
