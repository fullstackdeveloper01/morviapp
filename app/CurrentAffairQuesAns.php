<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentAffairQuesAns extends Model
{
    protected $table = 'current_affairs_question_answers';

    protected $fillable = ['calender_date', 'question', 'answer', 'option_1', 'option_2', 'option_3', 'option_4', 'question_language'];
}
