<?php


namespace App;


class LeaderZc extends Base
{
    public function user()
    {
        return $this->belongsTo(User::class,'u_id','id');
    }
}
