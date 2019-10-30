<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeOrder extends Base
{
    //
    public function items()
    {
        return $this->hasMany(CafeOrderMsg::class,'order_id','id');
    }
}
