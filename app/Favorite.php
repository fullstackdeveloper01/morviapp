<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorites';

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
		
    public function book(){
        return $this->belongsTo('App\Book', 'item_id');
    }
		
    public function note(){
        return $this->belongsTo('App\CoachingNote', 'item_id');
    }
}