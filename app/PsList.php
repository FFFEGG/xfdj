<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PsList extends Base
{
    //
    public function logps()
    {
        return $this->belongsTo(LogPs::class,'log_ps_id','id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
