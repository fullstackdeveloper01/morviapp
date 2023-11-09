<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'user_product_images';

    protected $fillable = ['id', 'product_id', 'image'];
}