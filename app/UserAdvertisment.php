<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class UserAdvertisment extends Model
{
    protected $table = 'user_advertisements';

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

}
