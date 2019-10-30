<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SyMsg extends Base
{
    //
    public function shkc()
    {
        return $this->belongsTo(ShKc::class,'sh_goods_id','id');
    }
}
