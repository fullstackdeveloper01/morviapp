<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class ProfessionalTableData extends Model
{
    protected $table = 'user_professional_table_data';

    public function category(){
        return $this->belongsTo('App\Category', 'category_table_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
