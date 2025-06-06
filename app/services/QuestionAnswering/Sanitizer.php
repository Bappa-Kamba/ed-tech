<?php

namespace App\Services\QuestionAnswering;

class Sanitizer
{
    public function sanitize(string $raw): string
    {
        // If </think> exists, return everything after it
        if (preg_match('/<\/think>(.*)/s', $raw, $matches)) {
            $clean = trim($matches[1]);
        } else {
            // Fallback: use the entire raw response
            $clean = trim($raw);
        }

        // Remove markdown-style bold (**text**)
        $clean = preg_replace('/\*\*(.*?)\*\*/', '$1', $clean);

        return $clean ?: 'Sorry, I could not find an answer.';
    }
}
