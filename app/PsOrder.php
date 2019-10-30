<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PsOrder extends Base
{
    //

    public function loguser()
    {
        return $this->belongsTo(LogPerson::class,'u_id','id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
