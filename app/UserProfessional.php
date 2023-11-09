<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class UserProfessional extends Model
{
    protected $table = 'user_professionals';

    public function category(){
        return $this->belongsTo('App\Category', 'category_id');
    }
}
