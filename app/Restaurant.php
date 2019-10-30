<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Base
{
    //
    public function catelist()
    {
        return $this->hasMany(CafeCate::class,'cafe_id','id');
    }
}
