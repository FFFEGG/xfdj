<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Group;
use App\Msg;
use App\Order;
use App\OrderMsg;
use App\Product;
use App\PsList;
use App\SyMsg;
use App\User;
use App\XsUser;
use Carbon\Carbon;
use App\FormId;
use App\Wechat;
use App\Merchant;
use Illuminate\Support\Facades\DB;

Route::get('/news/3', 'NewsController@cjwt');

Route::get('/', function () {
    return '幸福家家';
});


Route::get('/qrcode', 'NewsController@qrcode');

Route::get('/sh', function () {
    return '<h1 style="text-align: center;margin-top: 49%">请工作人员转发注册</h1>';
});

Route::get('/usershow', 'Api\UserController@show');
Route::get('/showmycode', 'UserController@showmycode')->middleware('openid');
Route::get('/leaderRegister', 'UserController@leaderRegister');
Route::get('/addressRegister', 'UserController@addressRegister');
Route::get('/shRegister', 'UserController@shRegister');


Route::get('/xfdj_admin/leader_pass', 'UserController@leader_pass');
Route::get('/xfdj_admin/leader_close', 'UserController@leader_close');
Route::get('/xfdj_admin/thd_pass', 'UserController@thd_pass');
Route::get('/xfdj_admin/thd_close', 'UserController@thd_close');


Route::get('/mark_admin/leader_pass', 'UserController@mark_leader_pass');
Route::get('/mark_admin/leader_close', 'UserController@mark_leader_close');
Route::get('/mark_admin/thd_pass', 'UserController@mark_thd_pass');
Route::get('/mark_admin/thd_close', 'UserController@mark_thd_close');



Route::get('/supplier_register', 'GysController@supplier_register');
Route::get('/supplier_login', 'GysController@supplier_login');
Route::post('/supplier_login_store', 'GysController@supplier_login_store');
Route::post('/commodity_store', 'CommodityController@commodity_store');


Route::post('/register_gys', 'GysController@register_gys');
Route::get('/news/{news}','NewsController@data');


Route::get('/industrys','GysController@Industry');
Route::any('/commodity','CommodityController@login');


Route::get('/changeyg', function (){
    \App\Product::where('star_time', '>', date('Y-m-d H:i:s',time()))->where('status','!=',2)->update([
        'is_yg' => false,
        'status' => 2
    ]);
    \App\Product::where('star_time', '<=', date('Y-m-d H:i:s',time()))->where('status','!=',1)->update([
        'is_yg' => false,
        'status' => 1
    ]);
    \App\Product::where('end_time', '<=', date('Y-m-d H:i:s',time()))->where('status','!=',3)->update([
        'is_yg' => false,
        'status' => 3
    ]);
    \App\User::where('user_type',2)->where('is_sh','!=',1)->update([
        'is_sh' => 1
    ]);
});
Route::get('/changeavatar', function (){
    \App\User::where('avatar','')->where('gender','!=',2)->update([
        'avatar' => 'https://xfdj.luckhome.xyz/uploads/images/dca796f0eeec5203c23a8fb9ec5bc6c5.png'
    ]);
    \App\User::where('avatar','')->where('gender',2)->update([
        'avatar' => 'https://xfdj.luckhome.xyz/uploads/images/2a00dd636cbd507680a6b139860f5110.png'
    ]);
    \App\User::where('avatar','https://wx.qlogo.cn/mmhead/Q3auHgzwzM4h4b2KibAP4PYyzBjeQLBghOzw6HZlX1VhoFFTIpC32Aw/0')->where('gender','!=',2)->update([
        'avatar' => 'https://xfdj.luckhome.xyz/uploads/images/dca796f0eeec5203c23a8fb9ec5bc6c5.png'
    ]);
    \App\User::where('avatar','https://wx.qlogo.cn/mmhead/Q3auHgzwzM4h4b2KibAP4PYyzBjeQLBghOzw6HZlX1VhoFFTIpC32Aw/0')->where('gender',2)->update([
        'avatar' => 'https://xfdj.luckhome.xyz/uploads/images/2a00dd636cbd507680a6b139860f5110.png'
    ]);
});



Route::prefix('supplier')->middleware('gys')->group(function () {
    Route::get('/index','GysController@index');
    Route::any('/goodsuploads','GysController@goodsuploads');
    Route::any('/edit','GysController@edit');
    Route::get('/goodslist','GysController@goodslist');
    Route::get('/orders','GysController@orders');
});


Route::prefix('commodity')->middleware('commodity')->group(function () {
    Route::get('/index','CommodityController@index');
    Route::get('/supplier_status','CommodityController@supplier_status');
    Route::any('/gysedit/{gys}','CommodityController@gysedit');
    Route::get('/gysgoodslist/{id}','CommodityController@gysgoodslist');
    Route::post('/goods_status','CommodityController@goods_status');
});

Route::prefix('logc')->middleware('ps')->group(function () {
    Route::get('/index','LogController@index');
    Route::get('/history','LogController@history');
    Route::get('/nops','LogController@nops');
    Route::get('/shorder','LogController@shorder');
    Route::get('/shorderhistory','LogController@shorderhistory');
    Route::get('/shordernops','LogController@shordernops');
    Route::post('/psordersd','LogController@psordersd');
    Route::post('/shoppsorder','LogController@shoppsorder');
});
Route::post('users/{user}/{type}', 'UserController@downloadQrcode');

Route::any('/log_login', 'LogController@log_login');
Route::get('/map', 'MapController@index')->middleware('under-construction');

Route::any('CommunityAgent','ActiveController@CommunityAgent');
Route::any('PickUpPoint','ActiveController@PickUpPoint');
Route::any('Suppliergys','ActiveController@Suppliergys');



Route::any('/laravels', 'TestController@laravels');


Route::get('/incrementgoods', function (){
    $list = \App\Product::whereStatus(1)
            ->where('cate_id','!=',6)
            ->where('cate_id','!=',7)
            ->where('cate_id','!=',8)
            ->get();
    foreach ($list as $v) {
        $num = rand(1,10);
        if ($num == 2) {
            if ($v->stock == 1) {
                $v->stock += rand(50,200);
            } else {
                $v->stock--;
            }
            $v->sales_num++;
            $v->save();
        }
    }
});


Route::get('/qrsh/orders', function (){

    $order = Order::where('ps_time','<',date('Y-m-d H:i:s',time()-7*24*60*60))->whereStatus(3)->get();
    foreach ($order as $vi) {
        $vi->status = 4;
        $vi->save();
        $str = '';
        $zprice = 0;//社区代理收益
        $thdzprice = 0;//提货点收益
        foreach (OrderMsg::with('goods')->where('order_id', $vi->id)->get() as $v) {
            $str .= Product::find($v->goods_id)['title'] . '; ';
            if ($v['goods']['sy_type'] == 0) {
                $zprice += $v['goods']['leader_sy'] * $v['goods']['price'] * $v['num'];
            } else {
                $zprice += $v['goods']['leader_sy'] * $v['num'];
            }
            $thdzprice += $v['goods']['group_sy'];
        }

//        Msg::sendmsg(3, $vi->tel, $str);
        //添加社区代理佣金
        $leader = User::where('is_sh', 1)->where('user_type','!=',0)->find($vi->leader_id);
        if ($leader) {
            $leader->money += $zprice;
            $leader->zmoney += $zprice;
            $leader->save();
            SyMsg::create([
                'u_id' => $leader->id,
                'msg' => '用户' . User::find($vi->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $zprice
            ]);
            //添加销售收益
            $xsuser = User::where('user_type', 2)->find($leader->p_id);
            if ($xsuser) {
                $xsuser->money += $vi['price'] * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->zmoney += $vi['price'] * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->save();
                SyMsg::create([
                    'u_id' => $xsuser->id,
                    'msg' => '社区代理' . $leader->nickname . '用户' . User::find($vi->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $vi['price'] * XsUser::whereUId($xsuser->id)->first()['tc']
                ]);
            }
        }

        //提货点收益
        $thd = User::where('user_type', 3)->find(Group::find($vi->group_id)->u_id);
        $psorder = DB::table('log_ps')->where('id',PsList::whereOrderId($vi->id)->first()->log_ps_id)->sharedLock()->first();
        if (!$psorder->is_js) {
            DB::table('log_ps')->where('id',PsList::whereOrderId($vi->id)->first()->log_ps_id)->update([
                'is_js' => 1
            ]);
            if ($thd) {
                $thd->money += $psorder->price;
                $thd->zmoney +=  $psorder->price;
                $thd->save();
                SyMsg::create([
                    'u_id' => $thd->id,
                    'msg' => '提货点收益-用户' . User::find($vi->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $psorder->price
                ]);
            }
        }

    }

});

