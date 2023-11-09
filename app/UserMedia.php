<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class UserMedia extends Model
{
    protected $table = 'user_medias';
	
	protected $fillable = ['id', 'user_id', 'media_type', 'media_name'];
}
