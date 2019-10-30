<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeGoodsSpec extends Base
{
    //
    public function cafegoods()
    {
        return $this->belongsTo(CafeGoods::class, 'cafe_goods_id');
    }
}
