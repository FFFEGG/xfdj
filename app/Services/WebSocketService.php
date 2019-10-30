<?php

namespace App\Services;

use App\Merchant;
use App\User;
use App\GoodsDb;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\Product;
/**
 * @see https://wiki.swoole.com/wiki/page/400.html
 */
class WebSocketService implements WebSocketHandlerInterface
{
    // 声明没有参数的构造函数
    public function __construct()
    {
    }
    public function onOpen(Server $server, Request $request)
    {
        // var_dump(app('swoole') === $server);// 同一实例
        $user = User::whereOpenid($request->get['openid'])->first();
        $userId = $user->id;
        app('swoole')->wsTable->set('uid:' . $userId, ['value' => $request->fd]);// 绑定uid到fd的映射
        app('swoole')->wsTable->set('fd:' . $request->fd, ['value' => $userId]);// 绑定fd到uid的映射
        $server->push($request->fd, json_encode('链接服务器成功'.$user->id));
    }
    public function onMessage(Server $server, Frame $frame)
    {
        $info = json_decode($frame->data);//接受收到的数据并转为object
        switch ($info->type) {
            //心跳包
            case "ping":
                $user = User::whereOpenid($info->openid)->first();
                $server->push(app('swoole')->wsTable->get('uid:'.$user->id,'value'),json_encode('pong'.date('Y-m-d H:i:s',time())));// 广播
                break;
            case "getbuylist":
            	$checkDayStr = date('Y-m-d ',time());
			    $timeBegin1 = strtotime($checkDayStr."09:00".":00");
			    $timeEnd1 = strtotime($checkDayStr."24:00".":00");
			   
			    $curr_time = time();
			   
			    if($curr_time >= $timeBegin1 && $curr_time <= $timeEnd1)
			    {
				    $user = User::whereOpenid($info->openid)->first();
	                $buy =  GoodsDb::inRandomOrder()->first();
	                $goods = Product::where('status',1)->where('cate_id','!=',6)->where('cate_id','!=',7)->where('cate_id','!=',8)->where('stock','>',0)->inRandomOrder()->first();
	                $buy->avatar = env('APP_URL').'/uploads/'. $buy->avatar;
	                $buy->nick =  mb_substr($buy->nick,0,1).'***'.mb_substr($buy->nick,mb_strlen($buy->nick,"utf-8")-1,mb_strlen($buy->nick,"utf-8"));
	                $buy['time'] = rand(5,20);
	                $buy['goods_id'] = $goods->id;
	                $data = [
	                	'type' => 'getbuylist',
	                	'data' => $buy
	                	];
	                $server->push(app('swoole')->wsTable->get('uid:'.$user->id,'value'),json_encode($data));// 广播
			    }

       
                break;
            case "closeorder":
                $server->push(app('swoole')->wsTable->get('uid:'.$info->sh_id,'value'),json_encode(['type' => 'closeroder']));// 广播
                break;
            case "successpay":
                $server->push(app('swoole')->wsTable->get('uid:'.$info->sh_id,'value'),json_encode(['type' => 'successpay']));// 广播
                break;
            //聊天消息
        }

    }
    public function onClose(Server $server, $fd, $reactorId)
    {
        $server->push($fd, 'Goodbye');
        $uid = app('swoole')->wsTable->get('fd:' . $fd);
        if ($uid !== false) {
            app('swoole')->wsTable->del('uid:' . $uid['value']);// 解绑uid映射
        }
        app('swoole')->wsTable->del('fd:' . $fd);// 解绑fd映射
        $server->push($fd, 'Goodbye');
    }
}
