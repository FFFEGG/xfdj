<?php

use App\Group;
use Illuminate\Support\MessageBag;

define('USER', '13377141250@sina.cn');	//*必填*：飞鹅云后台注册账号
define('UKEY', '4dkddw8qAF4gaPYP');	//*必填*: 飞鹅云注册账号后生成的UKEY
define('SN', '920533220');	    //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API


//以下参数不需要修改
define('IP','api.feieyun.cn');			//接口IP或域名
define('PORT',80);						//接口IP端口
define('PATH','/Api/Open/');		//接口路径


Route::get('/', 'HomeController@index');
Route::resource('/log_person', LogPersonController::class);
Route::resource('/order', OrderController::class);
Route::resource('/shorder', ShOrderController::class);
Route::any('/order_ps', 'DfhorderController@order_ps');
Route::any('/shorderps', 'ShOrderController@shorderps');


Route::get('/', 'HomeController@index');
Route::get('/ps', 'DfhorderController@ps');
Route::get('/getCateList',function () {
    $list = \App\GoodsCate::orderBy('sort','asc')->get(['id','name as text']);
    return $list;
});
Route::get('/getGysList',function () {
    $list = \App\Gys::where('status',1)->get(['id','name as text']);
    return $list;
});
Route::get('/api/users',function (\Illuminate\Http\Request $request) {
    $q = $request->get('q');
    return \App\User::where('openid', 'like', "%$q%")->paginate(null, ['id', 'nickname as text']);
});

Route::get('/getLeader',function () {
    $list = \App\User::where('is_leader',1)->get(['id','nickname as text']);
    return $list;
});


Route::get('/dy',function (\Illuminate\Http\Request $request) {
    $order = \App\Order::find($request->id);
    $orderInfo = '<C><DB>幸福家家</DB></C><BR>';
    $orderInfo .= '订单号:'.$order->sn.'<BR>';
    $orderInfo .= '团购结束时间:'.$order->items[0]->goods->end_time.'<BR>';
    $orderInfo .= '取货点:'.Group::find($order->group_id)->title.'<BR>';
    $orderInfo .= '取货点地址:'.Group::find($order->group_id)->address.'<BR>';
    $orderInfo .= '<C>-----------取件信息-------------</C><BR>';
    $orderInfo .= '<L>产品：'.$order->items[0]->goods->title.$order->items[0]->spec.'X'.$order->items[0]->num.'</L><BR>';
    $orderInfo .= '<L>取件人名称:'.$order->name.'</L><BR>';
    $orderInfo .= '<L>取件人电话:'.$order->tel.'</L><BR>';
    $orderInfo .= '--------------------------------<BR>';
    $orderInfo .= '<C>您的幸福已送达</C><BR>';
    $orderInfo .= '<C>请到小程序确认收货哦</C><BR>';
    $orderInfo .= '<QR>https://xfdj.luckhome.xyz/miniprojext_order</QR>';//把二维码字符串用标签套上即可自动生成二维码
    $orderInfo .= '--------------------------------<BR>';
    //打开注释可测试
    $rew = wp_print(SN,$orderInfo,1);

    if ($rew != 'error') {
        $success = new MessageBag([
            'title'   => '打印成功',
        ]);
        return back()->with(compact('success'));
    }
});

function addprinter($snlist){
    $time = time();			    //请求时间
    $content = array(
        'user'=>USER,
        'stime'=>$time,
        'sig'=>signature($time),
        'apiname'=>'Open_printerAddlist',

        'printerContent'=>$snlist
    );

    $client = new \App\Logistic\Controllers\HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        echo 'error';
    }
    else{
        echo $client->getContent();
    }

}

/*
 *  方法1
	拼凑订单内容时可参考如下格式
	根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
*/
function wp_print($printer_sn,$orderInfo,$times){
    $time = time();			    //请求时间
    $content = array(
        'user'=>USER,
        'stime'=>$time,
        'sig'=>signature($time),
        'apiname'=>'Open_printMsg',
        'sn'=>$printer_sn,
        'content'=>$orderInfo,
        'times'=>$times//打印次数
    );

    $client = new \App\Logistic\Controllers\HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        return 'error';
    }
    else{
        //服务器返回的JSON字符串，建议要当做日志记录起来
        return $client->getContent();
    }

}





/*
 *  方法2
	根据订单索引,去查询订单是否打印成功,订单索引由方法1返回
*/
function queryOrderState($index){
    $time = time();			    //请求时间
    $msgInfo = array(
        'user'=>USER,
        'stime'=>$time,
        'sig'=>signature($time),
        'apiname'=>'Open_queryOrderState',

        'orderid'=>$index
    );

    $client = new \App\Logistic\Controllers\HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }

}




/*
 *  方法3
	查询指定打印机某天的订单详情
*/
function queryOrderInfoByDate($printer_sn,$date){
    $time = time();			    //请求时间
    $msgInfo = array(
        'user'=>USER,
        'stime'=>$time,
        'sig'=>signature($time),
        'apiname'=>'Open_queryOrderInfoByDate',

        'sn'=>$printer_sn,
        'date'=>$date
    );

    $client = new \App\Logistic\Controllers\HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }

}



/*
 *  方法4
	查询打印机的状态
*/
function queryPrinterStatus($printer_sn){
    $time = time();			    //请求时间
    $msgInfo = array(
        'user'=>USER,
        'stime'=>$time,
        'sig'=>signature($time),
        'apiname'=>'Open_queryPrinterStatus',

        'sn'=>$printer_sn
    );

    $client = new \App\Logistic\Controllers\HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }
}

//生成签名
function signature($time){
    return sha1(USER.UKEY.$time);//公共参数，请求公钥
}
