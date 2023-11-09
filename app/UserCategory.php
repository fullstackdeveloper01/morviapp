<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class UserCategory extends Model
{
    protected $table = 'user_categories';
	
	protected $fillable = ['id', 'user_id', 'category_name', 'status'];
}
