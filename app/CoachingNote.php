<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CoachingNote extends Model{

    protected $table = 'coaching_notes';

    protected function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id');
    }

    protected function coaching()
    {
        return $this->belongsTo('App\User', 'coaching_id');
    }

    protected function subject()
    {
        return $this->belongsTo('App\CoachingSubject', 'subject_id');
    }
}