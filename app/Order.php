<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Base
{
    //
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function groups()
    {
        return $this->hasOne(Group::class,'group_id','id');
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class,'coupon_id','id');
    }


    public function items()
    {
        return $this->hasMany(OrderMsg::class);
    }

    public function ordergoods()
    {
        return $this->hasOne(OrderMsg::class,'order_id','id');
    }


    public function psorder()
    {
        return $this->hasOne(PsOrder::class,'order_id','id');
    }


    public function pslist()
    {
        return $this->hasOne(PsList::class,'order_id','id');
    }

    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('sn', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }


    public static function ordermsg()
    {

    }

}
