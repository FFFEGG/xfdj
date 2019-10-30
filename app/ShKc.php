<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShKc extends Base
{
    //
    public function goods()
    {
        return $this->belongsTo(Product::class,'goods_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'u_id','id');
    }
}
