<?php

namespace App\Services\QuestionAnswering;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatQAService implements QAServiceInterface
{
    public function answer(string $lessonContent, string $question): string
    {
        $apiKey = config('services.huggingface.api_key');

        $payload = [
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful tutor. Answer based on the lesson or say if the answer is unclear.'],
                ['role' => 'user', 'content' => "Lesson:\n" . $lessonContent . "\n\nQuestion:\n" . $question],
            ],
            'model' => 'deepseek-ai/DeepSeek-R1-0528',
            'temperature' => 0.7,
        ];

        // Log the request for debugging
        Log::info('ChatQAService request payload:', [
            'api_key' => $apiKey,
            'payload' => $payload,
        ]);

        $response = Http::withToken($apiKey)
            ->post('https://router.huggingface.co/hyperbolic/v1/chat/completions', $payload);

        $responseBody = $response->json();

        // Log the response for debugging
        Log::info('ChatQAService response:', [
            'status' => $response->status(),
            'body' => $responseBody,
            'answer' => $responseBody['choices'][0]['message']['content'],
        ]);

        return $responseBody['choices'][0]['message']['content'];
    }
}
