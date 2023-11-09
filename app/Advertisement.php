<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'advertisements';

    public function coaching(){
        return $this->belongsTo('App\User', 'coaching_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}