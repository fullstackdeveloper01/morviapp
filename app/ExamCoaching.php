<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamCoaching extends Model
{
    protected $table = 'exams_coaching';

    public function coaching(){
        return $this->belongsTo('App\Coaching', 'coaching_id');
    }
}