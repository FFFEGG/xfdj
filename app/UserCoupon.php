<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Base
{
    //
    public function yhq()
    {
        return $this->belongsTo(CouponCode::class,'coupon_id','id');
    }
}
