<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $table = 'categoriess';

    public function category(){
        return $this->belongsTo('App\Category', 'parent_id');
    }
}
