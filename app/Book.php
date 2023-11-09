<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    public function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id');
    }

    public function publisher()
    {
        return $this->belongsTo('App\User', 'publisher_id');
    }
}