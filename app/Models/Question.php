<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['lesson_id', 'user_id', 'question', 'answer', 'short_answer'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
