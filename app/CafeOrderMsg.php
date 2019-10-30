<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeOrderMsg extends Base
{
    //
    public function order()
    {
        return $this->belongsTo(CafeOrder::class,'order_id','id');
    }
}
