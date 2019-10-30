<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShSmOrder extends Base
{
    //
    public function shkc()
    {
        return $this->belongsTo(ShKc::class,'sh_goods_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'u_id','id');
    }
}
