<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Base
{
    //
    public function leader()
    {
        return $this->belongsTo(User::class,'u_id','id');
    }
}
