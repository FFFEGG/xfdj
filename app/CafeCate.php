<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeCate extends Model
{
    //
    public function cafe()
    {
        return $this->belongsTo(Restaurant::class,'cafe_id','id');
    }

    public function cafegoods()
    {
        return $this->hasMany(CafeGoods::class,'cate_id','id');
    }
}
