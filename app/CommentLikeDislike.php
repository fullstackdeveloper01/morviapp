<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentLikeDislike extends Model
{
    use HasFactory;
    protected $table = 'comment_like_dislike';
    protected $fillable = [
        'comment_id',
        'user_id',
    ];
}
