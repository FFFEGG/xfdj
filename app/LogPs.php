<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogPs extends Base
{
    //
    public function loguser()
    {
        return $this->belongsTo(LogPerson::class,'u_id','id');
    }

    public function items()
    {
        return $this->hasMany(PsList::class,'log_ps_id','id');
    }
}
