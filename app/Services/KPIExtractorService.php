<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KPIExtractorService
{
    public function extract(string $text): array
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->timeout(30)
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

        // 🧠 STEP 1: تأكد أن الرد موجود
        if (!$response->successful()) {
            dd([
                "status" => $response->status(),
                "body" => $response->body(),
                "headers" => $response->headers()
            ]);
        }
        $data = $response->json();

        $content = $data['choices'][0]['message']['content'] ?? null;

        // 🧠 STEP 2: لو فاضي
        if (!$content) {
            return $this->fallback("EMPTY_CONTENT");
        }

        // 🧠 STEP 3: حاول تفكيك JSON
        $parsed = json_decode($content, true);

        if (!is_array($parsed)) {
            return $this->fallback("INVALID_JSON", $content);
        }

        // 🧠 STEP 4: ضمان القيم
        return [
            "domain" => $parsed["domain"] ?? null,
            "intent" => $parsed["intent"] ?? "unknown",
            "target" => $parsed["target"] ?? null,
            "timeframe" => $parsed["timeframe"] ?? "unknown",
        ];
    }

    private function fallback($reason, $raw = null): array
    {
        return [
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
You are a STRICT KPI extraction engine.

Return ONLY valid JSON.

Format:
{
  "domain": string or null,
  "intent": "growth" | "reduce" | "maintain" | "unknown",
  "target": number or null,
  "timeframe": "week" | "month" | "year" | "unknown"
}

Rules:
- Do NOT explain anything
- Do NOT add extra keys
- If missing → use null or unknown
- Extract meaning from Arabic text
TXT;
    }
}
