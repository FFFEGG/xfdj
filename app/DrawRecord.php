<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrawRecord extends Base
{
    //
    public function goods()
    {
        return $this->belongsTo(ActiveGoods::class,'activegoods_id','id');
    }
}
