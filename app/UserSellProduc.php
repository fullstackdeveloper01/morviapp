<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSellProduc extends Model
{
    protected $table = 'user_sell_products';

    public function brand(){
        return $this->belongsTo('App\Brand', 'brand_id');
    }
}