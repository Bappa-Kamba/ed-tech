<?php

namespace App\Services\QuestionAnswering;

use App\Services\QuestionAnswering\QAServiceInterface;
use Illuminate\Support\Facades\Http;

/**
 * Extractive QA Service using Hugging Face's RoBERTa model.
 * This service extracts answers from a given context based on the provided question.
 */
class GenerativeQAService implements QAServiceInterface
{
    public function answer(string $question, string $context): string
    {
        $response = Http::withToken(config('services.huggingface.api_key'))
            ->post('https://api-inference.huggingface.co/models/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'inputs' => "Context: $context\n\nQuestion: $question\n\nAnswer:",
                'parameters' => ['max_new_tokens' => 100]
            ]);

        return $response['generated_text'] ?? 'Sorry, I could not generate a response.';
    }
}
