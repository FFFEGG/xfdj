<?php


namespace App;


class ThdZc extends Base
{
    //
    public function leader()
    {
        return $this->belongsTo(User::class,'u_id','id');
    }
}
