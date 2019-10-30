<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leader extends Base
{
    //
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
