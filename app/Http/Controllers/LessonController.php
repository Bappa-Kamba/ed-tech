<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Question;
use Config\services;
use App\Services\QuestionAnswering\QAServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\QuestionAnswering\Sanitizer;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['create', 'store']);

        $this->sanitizer = new Sanitizer();
    }

    public function create()
    {
        return view('lessons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Lesson::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('lessons.index')->with('success', 'Lesson created successfully!');
    }

    public function showOne($id)
    {
        $lesson = \App\Models\Lesson::findOrFail($id);
        $questions = \App\Models\Question::where('lesson_id', $lesson->id)->latest()->get();

        return view('lessons.show', compact('lesson', 'questions'));
    }

    public function showAll()
    {
        $lessons = \App\Models\Lesson::latest()->get();

        return view('lessons.index', compact('lessons'));
    }

    public function index(Lesson $lesson)
    {
        $questions = $lesson->questions()->latest()->take(10)->get();

        return response()->json($questions);
    }

    public function ask(Request $request, Lesson $lesson, QAServiceInterface $qaService)
    {
        $request->validate([
            'question' => 'required|string',
        ]);

        $context = $lesson->content;
        $questionText = $request->input('question');

        // Save the question to the database
        $question = Question::create([
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'question' => $questionText,
        ]);

        // Send to Hugging Face inference API
        $answer = $qaService->answer($context, $questionText);
        $short_answer = $this->sanitizer->sanitize($answer);

        // Log the response for debugging
        Log::info('Hugging Face API response:', [
            'question_id' => $question->id,
            'question' => $questionText,
            'answer' => $answer,
        ]);

        // Save the answer to the question
        $question->update(['answer' => $answer, 'short_answer' => $short_answer]);

        return response()->json([
            'answer' => $short_answer,
        ]);
    }
}
