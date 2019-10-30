<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class User extends Base
{

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function thds()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',3)
            ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
    }

    public function todaythds()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',3)
            ->whereBetween('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()]);
    }

    public function monthds()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',3)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
    }

    public function threemonthds()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',3)
            ->whereBetween('created_at',[Carbon::now()->modify('-90 days'),Carbon::today()]);
    }

    public function sqdls()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',1)
            ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
    }
    public function toadysqdls()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',1)
            ->whereBetween('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()]);
    }

    public function monsqdls()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',1)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
    }

    public function threemonsqdls()
    {
        return $this->hasMany(User::class, 'p_id','id')
            ->where('user_type',1)
            ->whereBetween('created_at',[Carbon::now()->modify('-90 days'),Carbon::today()]);
    }

    public function ywy()
    {
        return $this->hasOne(XsUser::class,'u_id','id');
    }

    public function fuser()
    {
        return $this->hasOne(User::class,'id','p_id');
    }
}
