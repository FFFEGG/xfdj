<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeGoods extends Base
{
    //
    public function specs()
    {
        return $this->hasMany(CafeGoodsSpec::class,'cafe_goods_id');
    }
}
