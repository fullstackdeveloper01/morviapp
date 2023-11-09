<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class InShort extends Model
{
    protected $table = 'in_shorts';

    protected $fillable = ['category_id','calender_date' ,'inShort_title' ,'short_language' ,'description' ,'image' ,'inShort_link'];
}
