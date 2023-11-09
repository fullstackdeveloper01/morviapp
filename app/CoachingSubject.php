<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class CoachingSubject extends Model{
    protected $table = 'coaching_subjects';

    protected function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id');
    }

    protected function coaching()
    {
        return $this->belongsTo('App\User', 'coaching_id');
    }
}