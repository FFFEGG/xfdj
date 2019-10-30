<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShOrderMsg extends Base
{
    //
    public function goods()
    {
        return $this->belongsTo(Product::class,'goods_id','id');
    }
}
