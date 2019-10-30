<?php

namespace App;

class Product extends Base
{
    //
    public function setPicsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['pics'] = json_encode($pictures);
        }
    }

    public function getPicsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

//    public function coupons()
//    {
//        return $this->belongsToMany(CouponCode::class,'coupons','id');
//    }
    public function getCouponsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setCouponsAttribute($value)
    {
        $this->attributes['coupons'] = implode(',', $value);
    }

    public function getRegionsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setRegionsAttribute($value)
    {
        $this->attributes['regions'] = implode(',', $value);
    }

    public function delStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }
        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addSalesNum($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('sales_num', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('stock', $amount);
    }

    public function addRealSales($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('real_sales', $amount);
    }


    public function getShsyAttribute()
    {
        return $this->sy_type == 0 ? round($this->leader_sy * $this->price + $this->group_sy,2) : round($this->group_sy + $this->leader_sy,2);
    }

    public function getTzsyAttribute()
    {
        return $this->sy_type == 0 ? round($this->leader_sy * $this->price,2) : round($this->group_sy,2);
    }

    public function specs()
    {
        return $this->hasMany(Spec::class,'goods_id','id');
    }

//    public function getTypeAttribute($value)
//    {
//        return explode(',', $value);
//    }
//
//    public function setTypeAttribute($value)
//    {
//        $this->attributes['type'] = implode(',', $value);
//    }
}
