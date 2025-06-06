<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Lesson;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Get all questions for a lesson, including answers
    public function index(Lesson $lesson)
    {
        // Assuming Question model has lesson_id foreign key
        $questions = Question::where('lesson_id', $lesson->id)
            ->orderBy('created_at', 'asc')
            ->get(['question', 'short_answer', 'created_at']);

        return response()->json(['questions' => $questions]);
    }
}
