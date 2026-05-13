<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    public function analyze(string $text): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'http://localhost',
            'X-Title' => 'Ghiras KPI System',
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [

            "model" => env('OPENROUTER_MODEL'),

            "messages" => [
                [
                    "role" => "system",
                    "content" => "
                    أنت AI متخصص بتحليل أهداف KPI.

                    حول النص إلى JSON فقط.

                    الصيغة المطلوبة:

                    {
                      \"domain\": string|null,
                      \"intent\": string|null,
                      \"target\": number|null,
                      \"timeframe\": string|null
                    }

                    domains:
                    volunteers
                    donations
                    education
                    health
                    employment

                    intents:
                    growth
                    reduction
                    improvement
                    support
                    training
                    "
                ],
                [
                    "role" => "user",
                    "content" => $text
                ]
            ],

            "temperature" => 0
        ]);

        if (!$response->successful()) {

            return [
                "success" => false,
                "status" => $response->status(),
                "body" => $response->body()
            ];
        }

        $data = $response->json();

        $content = $data['choices'][0]['message']['content'] ?? '{}';

        return json_decode($content, true);
    }
}
