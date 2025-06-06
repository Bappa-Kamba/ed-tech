<?php

namespace App\Services\QuestionAnswering;

use App\Services\QuestionAnswering\ExtractiveQAService;
use App\Services\QuestionAnswering\GenerativeQAService;
use App\Services\QuestionAnswering\QAServiceInterface;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid QA Service that combines extractive and generative approaches.
 * This service first attempts to extract an answer using an extractive model,
 * and if that fails, it falls back to a generative model.
 */
class HybridQAService implements QAServiceInterface
{
    protected ExtractiveQAService $extractive;
    protected GenerativeQAService $generative;

    public function __construct()
    {
        $this->extractive = new ExtractiveQAService();
        $this->generative = new GenerativeQAService();
    }

    public function answer(string $question, string $context): string
    {
        //log the question and context for debugging
        Log::info('HybridQAService called', [
            'question' => $question,
            'context' => $context,
        ]);
        $answer = $this->extractive->answer($question, $context);
        if ($answer !== null) {
            return $answer;
        }
        Log::info('Extractive QA failed, falling back to Generative QA', [
            'question' => $question,
            'context' => $context,
        ]);

        return $this->generative->answer($question, $context);
    }
}
