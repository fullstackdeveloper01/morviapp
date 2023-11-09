<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class CategoryTable extends Model
{
    protected $table = 'category_tables';

    public function category(){
        return $this->belongsTo('App\Category', 'category_id');
    }
    public function sub_category(){
        return $this->belongsTo('App\Category', 'sub_category_id');
    }
}
