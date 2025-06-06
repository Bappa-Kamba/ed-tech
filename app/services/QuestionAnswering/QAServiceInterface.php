<?php

namespace App\Services\QuestionAnswering;

interface QAServiceInterface
{
    public function answer(string $context, string $question): string | null;
}
