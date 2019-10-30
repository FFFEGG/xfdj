<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spec extends Base
{
    public function goods()
    {
        return $this->belongsTo(Product::class,'goods_id','id');
    }
}
