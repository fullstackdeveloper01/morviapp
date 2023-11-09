<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowUnFollow extends Model
{
    use HasFactory;
    protected $table = 'follow_unfollows';
    protected $fillable = [
        'user_id',
        'follower_id',
        'type',
        'post_id',
        'status',

    ];
}
