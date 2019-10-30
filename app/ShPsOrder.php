<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShPsOrder extends Base
{
    //
    public function order()
    {
        return $this->belongsTo(ShOrder::class,'order_id','id');
    }

//    public function shuser()
//    {
//        return $this->belongsTo(Merchant::class,'u_id','id');
//    }


    public function loguser()
    {
        return $this->belongsTo(LogPerson::class,'u_id','id');
    }
}
