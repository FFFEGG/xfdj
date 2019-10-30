<?php


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use DB;

class MongoController extends Controller
{
    public function index(){
        DB::connection('mongodb')       //选择使用mongodb
        ->collection('users')           //选择使用users集合
        ->insert([                          //插入数据
            'name'  =>  'tom',
            'age'     =>   18
        ]);
    }

    public function set()
    {
        $res = DB::connection('mongodb')->collection('users')->all();   //查询所有数据
        dd($res);
    }
}
