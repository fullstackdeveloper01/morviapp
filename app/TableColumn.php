<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class TableColumn extends Model
{
    protected $table = 'category_table_columns';

    public function tablename(){
        return $this->belongsTo('App\CategoryTable', 'category_table_id');
    }
}
