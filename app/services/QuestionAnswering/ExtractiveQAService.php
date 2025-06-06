<?php

namespace App\Services\QuestionAnswering;

use App\Services\QuestionAnswering\QAServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Extractive QA Service using Hugging Face's RoBERTa model.
 * This service extracts answers from a given context based on the provided question.
 */
class ExtractiveQAService implements QAServiceInterface
{
    public function answer(string $question, string $context): string | null
    {
        // log the question and context for debugging
        Log::info('ExtractiveQAService called', [
            'question' => $question,
            'context' => $context,
        ]);
        $response = Http::withToken(config('services.huggingface.api_key'))
            ->post('https://api-inference.huggingface.co/models/deepset/roberta-base-squad2', [
                'inputs' => [
                    'question' => $question,
                    'context' => $context,
                ]
            ]);

        $data = $response->json();

        Log::info('ExtractiveQAService response', [
            'response' => $data['answer'],
        ]);

        if (($data['score'] ?? 0) < 0.001) {
            return null;
        }

        return $data['answer'];
    }
}
