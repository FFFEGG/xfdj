<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShOrder extends Base
{
    //
    public function msg()
    {
        return $this->hasMany(ShOrderMsg::class,'order_id','id');
    }

    public function shpsorder()
    {
        return $this->hasOne(ShPsOrder::class,'order_id','id');
    }

    public function shuser()
    {
        return $this->hasOne(Merchant::class,'u_id','u_id');
    }
}
