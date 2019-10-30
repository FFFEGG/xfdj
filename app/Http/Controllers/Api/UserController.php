<?php


namespace App\Http\Controllers\Api;


use App\CouponCode;
use App\FormId;
use App\Group;
use App\Http\Controllers\Controller;
use App\Jobs\CloseOrder;
use App\Jobs\YsShOrder;
use App\Jobs\YsZdshOrder;
use App\LeaderZc;
use App\LogPs;
use App\Merchant;
use App\Msg;
use App\MsgCode;
use App\Order;
use App\OrderMsg;
use App\Product;
use App\PsList;
use App\PsOrder;
use App\Sfz;
use App\Spec;
use App\SyMsg;
use App\ThdZc;
use App\TxMsg;
use App\User;
use App\UserCoupon;
use App\XsUser;
use EasyWeChat;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use QrCode;
use WXBizDataCrypt;
use function EasyWeChat\Kernel\Support\generate_sign;

class UserController extends Controller
{

    public function __construct()
    {
        $config = [
            // 必要配置
            'app_id' => 'wxed65c3911947e645',
            'mch_id' => '1533565191',
            'secret'=> '6bac2b4155da598e2ba423a17b6ad471',
            'key' => 'KcacN4pVHn0VGWDzBy5INysCSmH07q1M',   // API 密钥
            'notify_url' => env('APP_URL') . '/api/wecaht_notify',     // 你也可以在下单时单独设置来想覆盖它
        ];

        $this->app = Factory::payment($config);

        $configs = [
            'app_id' => 'wxed65c3911947e645',
            'secret' => '6bac2b4155da598e2ba423a17b6ad471',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat1.log',
            ],
        ];

        $this->officialAccount = Factory::officialAccount($configs);
        $this->miniProgram = Factory::miniProgram($configs);
    }


    public function login(Request $request)
    {

        $miniProgram = \EasyWeChat::miniProgram();
        $rew = $miniProgram->auth->session($request->code);
        $user = User::firstOrCreate([
            'openid' => $rew['openid']
        ],[
            'avatar' => 'https://xfdj.luckhome.xyz/uploads/images/dca796f0eeec5203c23a8fb9ec5bc6c5.png',
            'nickname' => '幸福家家会员'
        ]);
        return $this->response->array([
            'data' => $rew,
            'user' => $user,
            'login' => config('is_login')
        ]);
    }

    public function getAccessToken(Request $request)
    {
        $sence = 'id='.$request->id.'&openid='.$request['userinfo']['id'];
        $response = $this->miniProgram->app_code->getUnlimit($sence, [
            'page'  => 'pages/goods',
            'width' => 600,
        ]);

        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->save('suncode','id='.$request->id.'&openid='.$request->openid.'.png');
        }
        return env('APP_URL').'/suncode/'.$filename;
    }


    public function getSessionKey(Request $request)
    {

        include_once "wxBizDataCrypt.php";
        $miniProgram = EasyWeChat::miniProgram();
        $rew = $miniProgram->auth->session($request->code);

        $appid = 'wxed65c3911947e645';
        $sessionKey = $rew['session_key'];
        $encryptedData = $request->encryptedData;
        $iv = $request->iv;
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            return $data;
        } else {
            return $errCode;
        }

    }

    public function updateUserInfo(Request $request)
    {

        $rew = User::where('openid', $request->openid)->update([
            'nickname' => $request->data['nickName'],
            'avatar' => $request->data['avatarUrl'],
            'gender' => $request->data['gender'],
        ]);
        if ($rew) {
            return 200;
        }
    }

    public function checkUserInfo(Request $request)
    {
        $user = User::whereOpenid($request->openid)->first();
        return $this->response->array([
            'isLeader' => $user->user_type == 1,
            'isUser' => $user->user_type == 0,
            'isXs' => $user->user_type == 2,
            'isThd' => $user->user_type == 3,
            'isSh' => $user->is_merchant,
            'num1' => Order::where('u_id',$user['id'])->where('status',0)->count(),
            'num2' => Order::where('u_id',$user['id'])->where('status',1)->count(),
            'num3' => Order::where('u_id',$user['id'])->where('status',2)->count(),
            'num4' => Order::where('u_id',$user['id'])->where('status',3)->count(),
            'num5' => Order::where('u_id',$user['id'])->where('status',4)->count(),
            'group_id' => Group::whereUId($user->id)->first()?Group::whereUId($user->id)->first()->id:0
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * 获取分享人信息
     */
    public function getleadeid($id,$userid)
    {
        if ($id) {
            $leader = User::whereOpenid($id)->first();
            if ($leader->user_type != 0) {
                return $leader->id;
            } else {
                return 0;
            }
        } else {
            $user = User::find($userid);
            if ($user->user_type != 0) {
                return $userid;
            }
            return 0;
        }

    }


    public function createOrder(Request $request)
    {
        //查询限购数量
        $list = Order::whereHas('items',function ($query )use ($request) {
            $query->where('goods_id',$request->goods_id);
        })->where('status','!=',0)->where('status','!=',-1)->where('u_id',$request['userinfo']['id'])->get();
        $xgnum = 0;

        foreach ($list as $v) {
            $xgnum += $v->items[0]['num'];
        }
        if ($xgnum +$request['num']  > (Product::find($request->goods_id)['is_xg']?Product::find($request->goods_id)['xg_num']:99999)) {
            return 400;
        }
        //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
        $leaderid = $request->leader_id ? $request->leader_id : 0;

        $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);

        $sn = Order::findAvailableNo();

        if ($request->spec_id) {
            $order_pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'] * $request->num;
        } else {
            $order_pirce = Product::find($request->goods_id)['price'] * $request->num;
        }
        $order = Order::create([
            'sn' => $sn,
            'u_id' => $request['userinfo']['id'],
            'group_id' => $request->group_id,
            'name' => $request->name,
            'tel' => $request->tel,
            'msg' => $request->msg,
            'price' => $order_pirce,
            'status' => 0,
            'hd_time' => Product::find($request->goods_id)['ps_time'],
            'leader_id' => $leader_id,
            'refund_status' => Order::REFUND_STATUS_PENDING
        ]);
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        OrderMsg::create([
            'order_id' => $order['id'],
            'goods_id' => $request->goods_id,
            'price' => $order_pirce / $request->num,
            'num' => $request->num,
            'spec_id' => $request->spec_id,
            'spec' => $request->spec_id?Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['name']:'',
            'is_pj' => false
        ]);

        foreach ($order->items as $item) {
            $item->goods->addSalesNum($item->num);
            $item->goods->addRealSales($item->num);
            $item->goods->delStock($item->num);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $order_pirce * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);
        $order->formid = $result['prepay_id'];
        $order->save();
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        } else {
            return $result;
        }


    }
    public function createOrderv2(Request $request)
    {
        //查询限购数量
        $list = Order::whereHas('items',function ($query )use ($request) {
            $query->where('goods_id',$request->goods_id);
        })->where('status','!=',0)->where('status','!=',-1)->where('u_id',$request['userinfo']['id'])->get();
        $xgnum = 0;

        foreach ($list as $v) {
            $xgnum += $v->items[0]['num'];
        }
        if ($xgnum +$request['num']  > (Product::find($request->goods_id)['is_xg']?Product::find($request->goods_id)['xg_num']:99999)) {
            return 400;
        }
        //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
        $leaderid = $request->leader_id ? $request->leader_id : 0;

        $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);

        $sn = Order::findAvailableNo();

        if ($request->spec_id) {
            $order_pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'] * $request->num;
        } else {
            $order_pirce = Product::find($request->goods_id)['price'] * $request->num;
        }
        $order = Order::create([
            'sn' => $sn,
            'u_id' => $request['userinfo']['id'],
            'group_id' => $request->group_id,
            'name' => $request->address['name'],
            'tel' => $request->address['tel'],
            'address' => $request->address['add'][0].$request->address['add'][1].$request->address['add'][2].$request->address['addadd'],
            'msg' => $request->msg,
            'price' => $order_pirce,
            'status' => 0,
            'hd_time' => Product::find($request->goods_id)['ps_time'],
            'leader_id' => $leader_id,
            'refund_status' => Order::REFUND_STATUS_PENDING
        ]);
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        OrderMsg::create([
            'order_id' => $order['id'],
            'goods_id' => $request->goods_id,
            'price' => $order_pirce / $request->num,
            'num' => $request->num,
            'spec_id' => $request->spec_id,
            'spec' => $request->spec_id?Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['name']:'',
            'is_pj' => false
        ]);

        foreach ($order->items as $item) {
            $item->goods->addSalesNum($item->num);
            $item->goods->addRealSales($item->num);
            $item->goods->delStock($item->num);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $order_pirce * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);
        $order->formid = $result['prepay_id'];
        $order->save();
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        } else {
            return $result;
        }


    }

    /**
     * 优惠券下单
     * @param Request $request
     * @return array|EasyWeChat\Kernel\Support\Collection|int|object|\Psr\Http\Message\ResponseInterface|string
     * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createOrderAddYhq(Request $request)
    {
        //查询限购数量
        $list = Order::whereHas('items',function ($query )use ($request) {
            $query->where('goods_id',$request->goods_id);
        })->where('status','!=',0)->where('u_id',$request['userinfo']['id'])->get();
        $xgnum = 0;

        foreach ($list as $v) {
            $xgnum += $v->items[0]['num'];
        }
        if ($xgnum +$request['num']  > (Product::find($request->goods_id)['is_xg']?Product::find($request->goods_id)['xg_num']:99999)) {
            return 400;
        }
        //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
        $leaderid = $request->leader_id ? $request->leader_id : 0;

        $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);

        $sn = Order::findAvailableNo();


        //产品单价
        if ($request->spec_id) {
            $order_pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'] * $request->num;
            $pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'];
        } else {
            $order_pirce = Product::find($request->goods_id)['price'] * $request->num;
            $pirce = Product::find($request->goods_id)['price'];
        }
        //检查优惠券
        $yhq = UserCoupon::whereUId($request['userinfo']['id'])->whereId($request->yhq)->where('is_used',0)->first();

        if ($yhq && CouponCode::find($yhq->coupon_id)->not_before <= date('Y-m-d H:i:s') && CouponCode::find($yhq->coupon_id)->not_after >= date('Y-m-d H:i:s')) {
            $order_pirce = CouponCode::find($yhq->coupon_id)->getAdjustedPrice($order_pirce);
            $yhq->is_used = true;
            $yhq->save();
        }

        $order = Order::create([
            'sn' => $sn,
            'u_id' => $request['userinfo']['id'],
            'group_id' => $request->group_id,
            'name' => $request->name,
            'tel' => $request->tel,
            'msg' => $request->msg,
            'price' => $order_pirce,
            'coupon_id' => $yhq? $yhq->id : 0,
            'status' => 0,
            'hd_time' => Product::find($request->goods_id)['ps_time'],
            'leader_id' => $leader_id,
            'refund_status' => Order::REFUND_STATUS_PENDING
        ]);
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        OrderMsg::create([
            'order_id' => $order['id'],
            'goods_id' => $request->goods_id,
            'price' => $pirce,
            'num' => $request->num,
            'spec_id' => $request->spec_id,
            'spec' => $request->spec_id?Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['name']:'',
            'is_pj' => false
        ]);

        foreach ($order->items as $item) {
            $item->goods->addSalesNum($item->num);
            $item->goods->addRealSales($item->num);
            $item->goods->delStock($item->num);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $order_pirce * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);
        $order->formid = $result['prepay_id'];
        $order->save();
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        } else {
            return $result;
        }


    }
    public function createOrderAddYhqv2(Request $request)
    {
        //查询限购数量
        $list = Order::whereHas('items',function ($query )use ($request) {
            $query->where('goods_id',$request->goods_id);
        })->where('status','!=',0)->where('u_id',$request['userinfo']['id'])->get();
        $xgnum = 0;

        foreach ($list as $v) {
            $xgnum += $v->items[0]['num'];
        }
        if ($xgnum +$request['num']  > (Product::find($request->goods_id)['is_xg']?Product::find($request->goods_id)['xg_num']:99999)) {
            return 400;
        }
        //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
        $leaderid = $request->leader_id ? $request->leader_id : 0;

        $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);

        $sn = Order::findAvailableNo();


        //产品单价
        if ($request->spec_id) {
            $order_pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'] * $request->num;
            $pirce = Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['price'];
        } else {
            $order_pirce = Product::find($request->goods_id)['price'] * $request->num;
            $pirce = Product::find($request->goods_id)['price'];
        }
        //检查优惠券
        $yhq = UserCoupon::whereUId($request['userinfo']['id'])->whereId($request->yhq)->where('is_used',0)->first();

        if ($yhq && CouponCode::find($yhq->coupon_id)->not_before <= date('Y-m-d H:i:s') && CouponCode::find($yhq->coupon_id)->not_after >= date('Y-m-d H:i:s')) {
            $order_pirce = CouponCode::find($yhq->coupon_id)->getAdjustedPrice($order_pirce);
            $yhq->is_used = true;
            $yhq->save();
        }

        $order = Order::create([
            'sn' => $sn,
            'u_id' => $request['userinfo']['id'],
            'group_id' => $request->group_id,
            'name' => $request->address['name'],
            'tel' => $request->address['tel'],
            'address' => $request->address['add'][0].$request->address['add'][1].$request->address['add'][2].$request->address['addadd'],
            'msg' => $request->msg,
            'price' => $order_pirce,
            'coupon_id' => $yhq? $yhq->id : 0,
            'status' => 0,
            'hd_time' => Product::find($request->goods_id)['ps_time'],
            'leader_id' => $leader_id,
            'refund_status' => Order::REFUND_STATUS_PENDING
        ]);
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        OrderMsg::create([
            'order_id' => $order['id'],
            'goods_id' => $request->goods_id,
            'price' => $pirce,
            'num' => $request->num,
            'spec_id' => $request->spec_id,
            'spec' => $request->spec_id?Spec::where('goods_id',$request->goods_id)->find($request->spec_id)['name']:'',
            'is_pj' => false
        ]);

        foreach ($order->items as $item) {
            $item->goods->addSalesNum($item->num);
            $item->goods->addRealSales($item->num);
            $item->goods->delStock($item->num);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $order_pirce * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);
        $order->formid = $result['prepay_id'];
        $order->save();
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        } else {
            return $result;
        }


    }

    public function createOrderByList(Request $request)
    {
        foreach ($request->goods_id as $k=>$v) {
            //查询限购数量
            $list = Order::whereHas('items',function ($query )use ($v) {
                $query->where('goods_id',$v);
            })->where('status','!=',0)
                ->where('status','!=',-1)
                ->where('u_id',$request['userinfo']['id'])->get();
            $xgnum = 0;

            foreach ($list as $vi) {
                $xgnum += $vi->items[0]['num'];
            }
//            dd(Product::find($v)['is_xg']?Product::find($v)['xg_num']:99999);
            if ($xgnum + $request['num'][$k] > (Product::find($v)['is_xg']?Product::find($v)['xg_num']:99999)) {
                return $this->response->array([
                    'key' => $k,
                    'code' => 400,
                    'msg' => Product::find($v)['title'].'超出限购数量'
                ]);
            }
        }
        //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
        $leaderid = $request->leader_id ? $request->leader_id : 0;

        $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);
        foreach ($request->goods_id as $k=>$v) {
            $goods[] = Product::whereIsSj(1)->find($v);
        }
        $zprice = 0;
        //按照到货时间添加订单
        foreach ($goods as $k => $v) {
            if ($request->spec_ids[$k]) {
                $zprice = ($zprice * 100 + Spec::find($request->spec_ids[$k])['price'] * $request->num[$k] * 100) / 100;
            } else {
                $zprice = ($zprice * 100 + $v->price * $request->num[$k] * 100) / 100;
            }
        }
        $sn = Order::findAvailableNo();
        foreach ($goods as $k => $v) {
//            dd($v->ps_time);
//            echo $v->ps_time.'|';
            $ps_time = $v->ps_time;
            $order_zprice = $v['price'] * $request->num[$k];
            $order = Order::create([
                'sn' => $sn,
                'u_id' => $request['userinfo']['id'],
                'group_id' => $request->group_id,
                'name' => $request->name,
                'tel' => $request->tel,
                'msg' => $request->msg,
                'price' => $order_zprice,
                'status' => 0,
                'hd_time' => $ps_time,
                'leader_id' => $leader_id,
                'refund_status' => Order::REFUND_STATUS_PENDING
            ]);
            $v->addSalesNum($request->num[$k]);
            $v->addRealSales($request->num[$k]);
            $v->delStock($request->num[$k]);
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
            OrderMsg::create([
                'order_id' => $order['id'],
                'goods_id' => $v['id'],
                'price' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['price']:$v->price,
                'num' => $request->num[$k],
                'spec_id' => $request->spec_ids[$k],
                'spec' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['name']:'',
                'is_pj' => false
            ]);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $zprice * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);

        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        }
    }


    public function createOrderByListAddYhq(Request $request)
    {
        //购物车的产品
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        if ($tmp_Users_tab) {
            $list = Redis::hvals($table_name);
            foreach ($list as $k => $v) {
                $list[$k] = json_decode($v);
            }
            $list = collect($list);
            $list = $list->map(function ($v) use ($table_name) {
                $goods = Product::find($v->goods_id);
                //商品单价
                if ($v->spec) {
                    $price = Spec::find($v->spec)->price;
                } else {
                    $price = $goods->price;
                }
                if ($v->is_check) {
                    return [
                        'id' => $goods->id,
                        'thumb' => env('APP_URL') . '/uploads/' . $goods->pics[0],
                        'title' => $goods->title,
                        'num' => $v->num,
                        'price' => $price,
                        'zprice' => max(0.01,round($price * $v->num - ($v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0),2)),
                        'spec' => $v->spec?Spec::find($v->spec)->name:'',
                        'spec_id' => $v->spec,
                        'is_check' => $v->is_check,
                        'ps_time' => $goods->ps_time,
                        'coupons' => $goods->coupons,
                        'coupon_id' => $v->coupon_id,
                        'yhq'=>$v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->name:'',
                        'week' => substr($goods->ps_time,0,10),
                        'xg_num' => $goods->is_xg? $goods->xg_num:$goods->stock,
                        'is_xg' => $goods->is_xg,
                        'zk' => $v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0
                    ];
                }
            });
            $sn = Order::findAvailableNo();
            foreach ($list as $v) {
                //结束限购
                if ($v['is_xg']) {
                    //判断用户是否超出限购数量
                    $xgnum = 0;
                    $xgorder =  Order::whereHas('items',function ($query )use ($v) {
                        $query->where('goods_id',$v['id']);
                    })->where('status','!=',0)
                    ->where('status','!=',-1)
                    ->where('u_id',$request['userinfo']['id'])->get();

                    $goods = Product::find($v['id']);
                    if (!$xgorder->isEmpty()) {
                        foreach ($xgorder as $vi) {
                            $xgnum += $vi->items[0]['num'];
                        }
                        //判断是否提交数量是否超出限购数量
                        if ($goods->xg_num < $v['num'] + $xgnum ) {
                            return $this->response->array([
                                'key' => $k,
                                'code' => 400,
                                'msg' => $goods['title'].'超出限购数量'
                            ]);
                        }
                    } else {
                        if ($goods->xg_num < $v['num']) {
                            return $this->response->array([
                                'key' => $k,
                                'code' => 400,
                                'msg' => $goods['title'].'超出限购数量'
                            ]);
                        }
                    }
                }
                $leaderid = $request->leader_id ? $request->leader_id : 0;

                $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);
                //社区代理
                //$leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;
                $ps_time = $v['ps_time'];
                $order_zprice = $v['price'] * $v['num'];
                $goods = Product::find($v['id']);
                //检查优惠券
                $yhq = UserCoupon::whereUId($request['userinfo']['id'])->whereId($v['coupon_id'])->where('is_used',0)->first();
                if ($yhq && CouponCode::find($yhq->coupon_id)->not_before <= date('Y-m-d H:i:s') && CouponCode::find($yhq->coupon_id)->not_after >= date('Y-m-d H:i:s')) {
                    $yhq->is_used = true;
                    $yhq->save();
                }
                $order = Order::create([
                    'sn' => $sn,
                    'u_id' => $request['userinfo']['id'],
                    'group_id' => $request->group_id,
                    'name' => $request->name,
                    'tel' => $request->tel,
                    'msg' => $request->msg,
                    'price' => $v['zprice'],
                    'status' => 0,
                    'hd_time' => $ps_time,
                    'coupon_id' => $v['coupon_id'],
                    'leader_id' => $leader_id,
                    'refund_status' => Order::REFUND_STATUS_PENDING
                ]);
                $goods->addSalesNum($v['num']);
                $goods->addRealSales($v['num']);
                $goods->delStock($v['num']);
                $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
                OrderMsg::create([
                    'order_id' => $order['id'],
                    'goods_id' => $v['id'],
                    'price' => $order_zprice,
                    'num' => $v['num'],
                    'spec_id' => $v['spec_id'],
                    'spec' => $v['spec'],
                    'is_pj' => false
                ]);
                //删除购物车
                $key = $v['id'] . $v['spec_id'];
                $Shopp = Redis::hexists($table_name, $key);
                if ($Shopp) {
                    Redis::hdel($table_name, $key);
                }
            }
            $result = $this->app->order->unify([
                'body' => '幸福到家商城订单-' . $sn,
                'out_trade_no' => $sn,
                'total_fee' => $list->sum('zprice') * 100,
//                'total_fee' => 1,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'openid' => $request['userinfo']['openid'],
            ]);
            if ($result['return_code'] === 'SUCCESS') {
                // 二次签名的参数必须与下面相同
                $params = [
                    'appId' => 'wxed65c3911947e645',
                    'timeStamp' => strval(time()),
                    'nonceStr' => $result['nonce_str'],
                    'package' => 'prepay_id=' . $result['prepay_id'],
                    'signType' => 'MD5',
                ];
                $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

                return $this->response()->array([
                    'code' => 200,
                    'id' => $order['id'],
                    'result' => $params,
                ]);
            }
        } else {
            return $this->response->array([
                'code' => 404,
                'msg' => '系统错误'
            ]);
        }

        foreach ($request->goods_id as $k=>$v) {
            //查询限购数量
            $list = Order::whereHas('items',function ($query )use ($v) {
                $query->where('goods_id',$v);
            })->where('status','!=',0)
                ->where('status','!=',-1)
                ->where('u_id',$request['userinfo']['id'])->get();
            $xgnum = 0;

            foreach ($list as $vi) {
                $xgnum += $vi->items[0]['num'];
            }
//            dd(Product::find($v)['is_xg']?Product::find($v)['xg_num']:99999);
            if ($xgnum + $request['num'][$k] > (Product::find($v)['is_xg']?Product::find($v)['xg_num']:99999)) {
                return $this->response->array([
                    'key' => $k,
                    'code' => 400,
                    'msg' => Product::find($v)['title'].'超出限购数量'
                ]);
            }
        }
        $leader_id = $request->leader_id ? (User::whereOpenid($request->leader_id)->first()['user_type'] != 0 ? User::whereOpenid($request->leader_id)->first()['id'] : 0) : 0;

        foreach ($request->goods_id as $k=>$v) {
            $goods[] = Product::whereIsSj(1)->find($v);
        }
        $zprice = 0;
        //按照到货时间添加订单
        foreach ($goods as $k => $v) {
            if ($request->spec_ids[$k]) {
                $zprice = ($zprice * 100 + Spec::find($request->spec_ids[$k])['price'] * $request->num[$k] * 100) / 100;
            } else {
                $zprice = ($zprice * 100 + $v->price * $request->num[$k] * 100) / 100;
            }
        }
        $sn = Order::findAvailableNo();
        foreach ($goods as $k => $v) {
            $ps_time = $v->ps_time;
            $order_zprice = $v['price'] * $request->num[$k];
            $order = Order::create([
                'sn' => $sn,
                'u_id' => $request['userinfo']['id'],
                'group_id' => $request->group_id,
                'name' => $request->name,
                'tel' => $request->tel,
                'msg' => $request->msg,
                'price' => $order_zprice,
                'status' => 0,
                'hd_time' => $ps_time,
                'leader_id' => $leader_id,
                'refund_status' => Order::REFUND_STATUS_PENDING
            ]);
            $v->addSalesNum($request->num[$k]);
            $v->addRealSales($request->num[$k]);
            $v->delStock($request->num[$k]);
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
            OrderMsg::create([
                'order_id' => $order['id'],
                'goods_id' => $v['id'],
                'price' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['price']:$v->price,
                'num' => $request->num[$k],
                'spec_id' => $request->spec_ids[$k],
                'spec' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['name']:'',
                'is_pj' => false
            ]);
        }

        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $zprice * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);

        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        }
    }
    public function createOrderByListAddYhqv2(Request $request)
    {
        //购物车的产品
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        if ($tmp_Users_tab) {
            $list = Redis::hvals($table_name);
            foreach ($list as $k => $v) {
                $list[$k] = json_decode($v);
            }
            $list = collect($list);
            $list = $list->map(function ($v) use ($table_name) {
                $goods = Product::find($v->goods_id);
                //商品单价
                if ($v->spec) {
                    $price = Spec::find($v->spec)->price;
                } else {
                    $price = $goods->price;
                }
                if ($v->is_check) {
                    return [
                        'id' => $goods->id,
                        'thumb' => env('APP_URL') . '/uploads/' . $goods->pics[0],
                        'title' => $goods->title,
                        'num' => $v->num,
                        'price' => $price,
                        'zprice' => max(0.01,round($price * $v->num - ($v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0),2)),
                        'spec' => $v->spec?Spec::find($v->spec)->name:'',
                        'spec_id' => $v->spec,
                        'is_check' => $v->is_check,
                        'ps_time' => $goods->ps_time,
                        'coupons' => $goods->coupons,
                        'coupon_id' => $v->coupon_id,
                        'yhq'=>$v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->name:'',
                        'week' => substr($goods->ps_time,0,10),
                        'xg_num' => $goods->is_xg? $goods->xg_num:$goods->stock,
                        'is_xg' => $goods->is_xg,
                        'zk' => $v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0
                    ];
                }
            });
            $sn = Order::findAvailableNo();
            $order_zprice = 0;
            foreach ($list as $v) {
                  //  结束限购
                if ($v['is_xg']) {
                    //判断用户是否超出限购数量
                    $xgnum = 0;
                    $xgorder =  Order::whereHas('items',function ($query )use ($v) {
                        $query->where('goods_id',$v['id']);
                    })->where('status','!=',0)
                    ->where('status','!=',-1)
                    ->where('u_id',$request['userinfo']['id'])->get();

                    $goods = Product::find($v['id']);
                    if (!$xgorder->isEmpty()) {
                        foreach ($xgorder as $vi) {
                            $xgnum += $vi->items[0]['num'];
                        }
                        //判断是否提交数量是否超出限购数量
                        if ($goods->xg_num < $v['num'] + $xgnum ) {
                            return $this->response->array([
                                'key' => $k,
                                'code' => 400,
                                'msg' => $goods['title'].'超出限购数量'
                            ]);
                        }
                    } else {
                        if ($goods->xg_num < $v['num']) {
                            return $this->response->array([
                                'key' => $k,
                                'code' => 400,
                                'msg' => $goods['title'].'超出限购数量'
                            ]);
                        }
                    }
                }
                $ps_time = $v['ps_time'];
                $order_zprice += $v['price'] * $v['num'];
            }

            $leaderid = $request->leader_id ? $request->leader_id : 0;
            $leader_id = $this->getleadeid($leaderid,$request['userinfo']['id']);

            //检查优惠券
//            $yhq = UserCoupon::whereUId($request['userinfo']['id'])->whereId($v['coupon_id'])->where('is_used',0)->first();
//            if ($yhq && CouponCode::find($yhq->coupon_id)->not_before <= date('Y-m-d H:i:s') && CouponCode::find($yhq->coupon_id)->not_after >= date('Y-m-d H:i:s')) {
//                $yhq->is_used = true;
//                $yhq->save();
//            }
            $order = Order::create([
                'sn' => $sn,
                'u_id' => $request['userinfo']['id'],
                'group_id' => $request->group_id,
                'name' => $request->address['name'],
                'tel' => $request->address['tel'],
                'address' => $request->address['add'][0].$request->address['add'][1].$request->address['add'][2].$request->address['addadd'],
                'msg' => $request->msg,
                'price' => $order_zprice,
                'status' => 0,
                'hd_time' => $ps_time,
                'coupon_id' => 0,
                'leader_id' => $leader_id,
                'refund_status' => Order::REFUND_STATUS_PENDING
            ]);
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

            foreach ($list as $v) {
                $goods = Product::find($v['id']);
                OrderMsg::create([
                    'order_id' => $order['id'],
                    'goods_id' => $v['id'],
                    'price' => $order_zprice,
                    'num' => $v['num'],
                    'spec_id' => $v['spec_id'],
                    'spec' => $v['spec'],
                    'is_pj' => false
                ]);
                $goods->addSalesNum($v['num']);
                $goods->addRealSales($v['num']);
                $goods->delStock($v['num']);
                //删除购物车
                $key = $v['id'] . $v['spec_id'];
                $Shopp = Redis::hexists($table_name, $key);
                if ($Shopp) {
                    Redis::hdel($table_name, $key);
                }
            }
            $result = $this->app->order->unify([
                'body' => '幸福到家商城订单-' . $sn,
                'out_trade_no' => $sn,
                'total_fee' => $list->sum('zprice') * 100,
//                'total_fee' => 1,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'openid' => $request['userinfo']['openid'],
            ]);
            if ($result['return_code'] === 'SUCCESS') {
                // 二次签名的参数必须与下面相同
                $params = [
                    'appId' => 'wxed65c3911947e645',
                    'timeStamp' => strval(time()),
                    'nonceStr' => $result['nonce_str'],
                    'package' => 'prepay_id=' . $result['prepay_id'],
                    'signType' => 'MD5',
                ];
                $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

                return $this->response()->array([
                    'code' => 200,
                    'id' => $order['id'],
                    'result' => $params,
                ]);
            }
        } else {
            return $this->response->array([
                'code' => 404,
                'msg' => '系统错误'
            ]);
        }
    }


    public function getOrderList(Request $request)
    {
        $all = Order::with(['items', 'items.goods'])
            ->where('u_id', $request['userinfo']['id'])
            ->where('status', '!=',-1)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $all = $all->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'spec' => $vi->spec,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });

        $dfk = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', 0)->orderBy('created_at', 'desc')->paginate(15);

        $dfk = $dfk->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });

        $dfh = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', 1)->orderBy('created_at', 'desc')->paginate(15);

        $dfh = $dfh->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });

        $psz = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', 2)->orderBy('created_at', 'desc')->paginate(15);

        $psz = $psz->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });


        $dth = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', 3)->orderBy('created_at', 'desc')->paginate(15);

        $dth = $dth->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });

        $yth = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', 4)->orderBy('created_at', 'desc')->paginate(15);

        $yth = $yth->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });
        $qx = Order::with(['items', 'items.goods'])->where('u_id', $request['userinfo']['id'])->where('status', -1)->orderBy('created_at', 'desc')->paginate(15);

        $qx = $qx->map(function ($v) {
            return [
                'id' => $v->id,
                'time' => $v->created_at->format('Y-m-d H:i:s'),
                'status' => $v->status,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $v->price,
                'is_ys' => $v->is_ys,
                'statusName' => $this->statusname($v->status)
            ];
        });
        return $this->response->array([
            'data' => $all,
            'dfk' => $dfk,
            'dfh' => $dfh,
            'psz' => $psz,
            'dth' => $dth,
            'yth' => $yth,
            'qx' => $qx,
        ]);
    }


    public function getOrderData(Request $request)
    {
        $order = Order::with(['items', 'items.goods', 'group'])->whereUId($request['userinfo']['id'])
            ->where('id', $request->id)->first();


        $zprice = 0;

        foreach ($order->items as $v) {
            $zprice +=  $v->price * $v->num;
        }

        return $this->response->array([
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'sn' => $order->sn.$order->id,
                'payment_no' => $order->payment_no,
                'statusname' => $this->statusname($order->status),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'fk_time' => $order->paid_at,
                'items' => $order->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                        'spec' => $vi->spec,
                    ];
                }),
                'zprice' => round($zprice,2),
                'order_price' => $order->price,
                'group' => $order->group,
                'name' => $order->name,
                'tel' => $order->tel,
                'address' => $order->address,
                'closed' => $order->closed,
                'ps_time' => $order->ps_time,
                'coupon_id' => $order->coupon_id,
                'yhq' => $order->coupon_id ? UserCoupon::find($order->coupon_id)->yhq: '',
                'discount' => $order->coupon_id ? UserCoupon::find($order->coupon_id)->yhq->getAdPrice(round($zprice,2)): 0,
                'close_time' => strtotime($order->created_at) + 30 * 60 - time()
            ]
        ]);
    }

    public function statusname($status)
    {
        switch ($status) {
            case 0:
                return '未付款';
                break;
            case 1:
                return '待发货';
                break;
            case 2:
                return '配送中';
                break;
            case 3:
                return '待提货';
                break;
            case 4:
                return '已提货';
                break;
            case -1:
                return '已取消';
                break;

        }
    }

    public function payNowWfk(Request $request)
    {
        $list = Order::whereHas('items',function ($query )use ($request) {
            $query->where('goods_id',$request->id);
        })->where('status','!=',0)->where('status','!=',-1)->where('u_id',$request['userinfo']['id'])->get();
        $xgnum = 0;

        foreach ($list as $v) {
            $xgnum += $v->items[0]['num'];
        }
        if ($xgnum +$request['num']  > (Product::find($request->goods_id)['is_xg']?Product::find($request->goods_id)['xg_num']:99999)) {
            return 400;
        }


        $order = Order::whereId($request->id)->whereUId($request['userinfo']['id'])->first();
        $sn = Order::findAvailableNo();
        $result = $this->app->order->unify([
            'body' => '幸福到家商城订单-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $order->price * 100,
//            'total_fee' => 1,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $request['userinfo']['openid'],
        ]);
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            $order->sn = $sn;
            $order->save();
            $order = Order::whereSn($sn)->first();
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
            // 二次签名的参数必须与下面相同
            $params = [
                'appId' => 'wxed65c3911947e645',
                'timeStamp' => strval(time()),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            return $this->response()->array([
                'code' => 200,
                'id' => $order['id'],
                'result' => $params,
            ]);
        }
    }


    public function imgUpload(Request $request)
    {
        $path = $request->file('file')->store('public');
        return env('APP_URL') . '/storage/' . substr($path, 7, 60);
    }

    public function LeaderZcZf(Request $request)
    {
        $code = MsgCode::where('tel', $request->tel)->orderBy('created_at', 'desc')->first();
        if ($request->code != $code->code) {
            return 400;
        }

        if (time() > strtotime($code->created_at) + 10 * 60) {
            return 404;
        }
        $user = User::whereOpenid($request->openid)->first();
        $user->p_id = User::whereOpenid($request->f_openid)->first()?User::whereOpenid($request->f_openid)->first()['id']:0;
        $user->user_type = 1;//社区代理
        $user->save();
        $rew = LeaderZc::create([
            'u_id' => $user->id,
            'name' => $request->name,
            'tel' => $request->tel,
            'sfznum' => $request->sfznum,
            'msg' => $request->msg,
        ]);
        if ($rew) {
            return 200;
        }
    }


    public function LeaderZcZfcode(Request $request)
    {

        $user = User::whereOpenid($request->openid)->first();
        $user->p_id = $request->f_openid;
        $user->user_type = 1;//社区代理
        $user->save();
        $rew = LeaderZc::create([
            'u_id' => $user->id,
            'name' => $request->name,
            'tel' => $request->tel,
            'sfzz' => $request->sfzZ,
            'sfzf' => $request->sfzF,
            'sfzsc' => $request->sfzSC,
            'msg' => $request->msg,
        ]);
        if ($rew) {
            return 200;
        }
    }


    public function thdzczf(Request $request)
    {
        $code = MsgCode::where('tel', $request->tel)->orderBy('created_at', 'desc')->first();
        if ($request->code != $code->code) {
            return 400;
        }

        if (time() > strtotime($code->created_at) + 10 * 60) {
            return 404;
        }
        $user = User::whereOpenid($request->openid)->first();
        $user->p_id = User::whereOpenid($request->f_openid)->first()['id'];
        $user->user_type = 3;//提货点
        $user->save();
        $rew = ThdZc::create([
            'u_id' => $user->id,
            'name' => $request->name,
            'tel' => $request->tel,
            'shopname' => $request->shopname,
            'xqname' => $request->xqname,
            'address' => $request->address,
            'sfzz' => $request->sfzz,
            'sfzf' => $request->sfzf,
            'sfzsc' => $request->sfzsc,
            'yyzz' => $request->yyzz,
            'mtz' => $request->mtz,
            'msg' => $request->msg,
        ]);
        if ($rew) {
            return 200;
        }
    }

    public function shzczf (Request $request)
    {
        $code = MsgCode::where('tel', $request->tel)->orderBy('created_at', 'desc')->first();
        if ($request->code != $code->code) {
            return 400;
        }
        if (time() > strtotime($code->created_at) + 10 * 60) {
            return 404;
        }
        $user = User::whereOpenid($request->openid)->first();
        $user->f_id = User::whereOpenid($request->f_openid)->first()['id'];
        $user->save();
        $rew = Merchant::create([
            'u_id' => $user->id,
            'name' => $request->name,
            'tel' => $request->tel,
            'shopname' => $request->shopname,
            'xqname' => $request->xqname,
            'address' => $request->address,
            'yyzz' => $request->yyzz,
            'sfzZ' => $request->sfzZ,
            'sfzF' => $request->sfzF,
            'sfzSC' => $request->sfzSC
        ]);
        if ($rew) {
            return 200;
        }
    }

    public function thdzczfcode(Request $request)
    {
        $code = MsgCode::where('tel', $request->tel)->orderBy('created_at', 'desc')->first();
        if ($request->code != $code->code) {
            return 400;
        }

        if (time() > strtotime($code->created_at) + 10 * 60) {
            return 404;
        }

        $user = User::whereOpenid($request->openid)->first();
        $user->p_id = $request->f_openid;
        $user->user_type = 3;//提货点
        $user->save();
        $rew = ThdZc::create([
            'u_id' => $user->id,
            'name' => $request->name,
            'tel' => $request->tel,
            'shopname' => $request->shopname,
            'xqname' => $request->xqname,
            'address' => $request->address,
            'sfzz' => $request->sfzz,
            'sfzf' => $request->sfzf,
            'sfzsc' => $request->sfzsc,
            'yyzz' => $request->yyzz,
            'mtz' => $request->mtz,
            'msg' => $request->msg,
        ]);
        if ($rew) {
            return 200;
        }
    }

    public function findMyLeaderMsg(Request $request)
    {
        $msg = LeaderZc::whereUId($request['userinfo']['id'])->orderBy('created_at', 'desc')->first();

        if (!$msg || $msg->is_sh == -1) {
            return $this->response()->array([
                'code' => 200,
                'userinfo' => $request['userinfo']
            ]);
        } else {
            return $this->response()->array([
                'code' => 400,
                'userinfo' => $request['userinfo']
            ]);
        }
    }

    public function findMyShMsg(Request $request)
    {
        $msg = Merchant::whereUId($request['userinfo']['id'])->orderBy('created_at', 'desc')->first();

        if ($msg) {
            return $this->response()->array([
                'code' => 400,
                'userinfo' => $request['userinfo']
            ]);
        } else {
            return $this->response()->array([
                'code' => 200,
                'userinfo' => $request['userinfo']
            ]);
        }
    }

    public function findMyThdMsg(Request $request)
    {
        $msg = ThdZc::whereUId($request['userinfo']['id'])->orderBy('created_at', 'desc')->first();
        if (!$msg || $msg->is_sh == -1) {
            return $this->response()->array([
                'code' => 200,
                'userinfo' => $request['userinfo']
            ]);
        } else {
            return $this->response()->array([
                'code' => 400,
                'userinfo' => $request['userinfo']
            ]);
        }
    }


    public function getthdlist(Request $request)
    {

        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $list = Order::with(['items', 'items.goods'])->whereIn('status', [2, 3, 4])->whereGroupId($group_id)->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'sn' => $v->sn,
                'ps_time' => $v->ps_time,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'group_sy' => $vi->goods->group_sy
                    ];
                }),
                'name' => $v->name,
                'tel' => $v->tel,
                'status' => $v->status,
                'is_check' => false,
                'statusname' => $this->statusname($v->status)
            ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function calluser(Request $request)
    {
        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $order = Order::whereId($request->id)->whereGroupId($group_id)->first();
        $order->status = 3;
        $order->save();
//        $str = '';
//        foreach (OrderMsg::where('order_id', $order->id)->get() as $v) {
//            $str .= Product::find($v->goods_id)['title'] . '; ';
//        }
//        Wechat::msg()->template_message->send([
//            'touser' => User::find($order->u_id)->openid,
//            'template_id' => 'LKBPic75qPhiVr8c1o-LcpBKFIOQeR5EcybRGYIIYyA',
//            'page' => '/pages/orderdata?id=' . $order->id,
//            'form_id' => $order->formid,
//            'data' => [
//                'keyword1' => $str,
//                'keyword2' => Group::find($order->group_id)['title'] . Group::find($order->group_id)['address'],
//                'keyword3' => '取货时核对商品',
//                'keyword4' => $order->tel
//            ],
//        ]);
        $str = $order->items[0]->goods->title;
        Msg::sendmsg(2, $order->tel,$str);
        $psorder = PsOrder::whereOrderId($order->id)->first();
        $psorder->status = 1;
        $psorder->save();
        return $this->response()->array([
            'code' => 200
        ]);
    }

    public function th(Request $request)
    {
        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $order = Order::whereId($request->sn)->whereGroupId($group_id)->first();
        return $this->response->array([
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'sn' => $order->sn,
                'payment_no' => $order->payment_no,
                'statusname' => $this->statusname($order->status),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'fk_time' => $order->paid_at,
                'items' => $order->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'price' => $vi->price,
                    ];
                }),
                'zprice' => $order->price,
                'group' => $order->group,
                'name' => $order->name,
                'tel' => $order->tel,
                'closed' => $order->closed,
                'ps_time' => $order->ps_time,
                'close_time' => strtotime($order->created_at) + 30 * 60 - time()
            ]
        ]);
    }

    public function thById(Request $request)
    {
        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $order = Order::whereId($request->id)->whereGroupId($group_id)->first();

        $order->status = 4;
        $order->save();
        $str = '';
        $zprice = 0;//社区代理收益
        $thdzprice = 0;//提货点收益
        foreach (OrderMsg::with('goods')->where('order_id', $order->id)->get() as $v) {
            $str .= Product::find($v->goods_id)['title'] . '; ';
            if ($v['goods']['sy_type'] == 0) {
                $zprice += $v['goods']['leader_sy'] * $v['goods']['price'] * $v['num'];
            } else {
                $zprice += $v['goods']['leader_sy'] * $v['num'];
            }
            $thdzprice += $v['goods']['group_sy'];
        }

        Msg::sendmsg(3, $order->tel, $str);
        //添加社区代理佣金
        $leader = User::where('is_sh', 1)->where('user_type','!=',0)->find($order->leader_id);
        if ($leader) {
            $leader->money += $zprice;
            $leader->zmoney += $zprice;
            $leader->save();
            SyMsg::create([
                'u_id' => $leader->id,
                'msg' => '用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $zprice
            ]);
            //添加销售收益
            $xsuser = User::where('user_type', 2)->find($leader->p_id);
            if ($xsuser) {
                $xsuser->money += $zprice * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->zmoney += $zprice * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->save();
                SyMsg::create([
                    'u_id' => $xsuser->id,
                    'msg' => '社区代理' . $leader->nickname . '用户' . User::find($order->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $zprice * XsUser::whereUId($xsuser->id)->first()['tc']
                ]);
            }
        }


        //提货点收益
        $thd = User::where('user_type', 3)->find($request['userinfo']['id']);
        if ($thd) {
            $thd->money += $thdzprice;
            $thd->zmoney += $thdzprice;
            $thd->save();
            SyMsg::create([
                'u_id' => $thd->id,
                'msg' => '用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $thdzprice
            ]);
        }
        return 200;
    }



    public function thByUserId(Request $request)
    {
//        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $order = Order::whereId($request->id)->whereUId($request['userinfo']['id'])->first();

        $order->status = 4;
        $order->save();
        $str = '';
        $zprice = 0;//社区代理收益
        $thdzprice = 0;//提货点收益
        foreach (OrderMsg::with('goods')->where('order_id', $order->id)->get() as $v) {
            $str .= Product::find($v->goods_id)['title'] . '; ';
            if ($v['goods']['sy_type'] == 0) {
                $zprice += $v['goods']['leader_sy'] * $v['goods']['price'] * $v['num'];
            } else {
                $zprice += $v['goods']['leader_sy'] * $v['num'];
            }
            $thdzprice += $v['goods']['group_sy'];
        }

        Msg::sendmsg(3, $order->tel, $str);
        //添加社区代理佣金
        $leader = User::where('is_sh', 1)->where('user_type','!=',0)->find($order->leader_id);
        if ($leader) {
            $leader->money += $zprice;
            $leader->zmoney += $zprice;
            $leader->save();
            SyMsg::create([
                'u_id' => $leader->id,
                'msg' => '用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $zprice
            ]);
            //添加销售收益
            $xsuser = User::where('user_type', 2)->find($leader->p_id);
            if ($xsuser) {
                $xsuser->money += $zprice * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->zmoney += $zprice * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->save();
                SyMsg::create([
                    'u_id' => $xsuser->id,
                    'msg' => '社区代理' . $leader->nickname . '用户' . User::find($order->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $zprice * XsUser::whereUId($xsuser->id)->first()['tc']
                ]);
            }
        }


        //提货点收益
        $thd = User::where('user_type', 3)->find(Group::find($order->group_id)->u_id);
        if ($thd) {
            $thd->money += $thdzprice;
            $thd->zmoney += $thdzprice;
            $thd->save();
            SyMsg::create([
                'u_id' => $thd->id,
                'msg' => '用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $thdzprice
            ]);
        }
        return 200;
    }


    public function thByUserIdAddThd(Request $request)
    {
//        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $order = Order::whereId($request->id)->whereUId($request['userinfo']['id'])->first();

        $order->status = 4;
        $order->save();
        $str = '';
        $zprice = 0;//社区代理收益
        $thdzprice = 0;//提货点收益
        foreach (OrderMsg::with('goods')->where('order_id', $order->id)->get() as $v) {
            $str .= Product::find($v->goods_id)['title'] . '; ';
            if ($v['goods']['sy_type'] == 0) {
                $zprice += $v['goods']['leader_sy'] * $v['goods']['price'] * $v['num'];
            } else {
                $zprice += $v['goods']['leader_sy'] * $v['num'];
            }
            $thdzprice += $v['goods']['group_sy'];
        }

        Msg::sendmsg(3, $order->tel, $str);
        //添加社区代理佣金
        $leader = User::where('is_sh', 1)->where('user_type','!=',0)->find($order->leader_id);
        if ($leader) {
            $leader->money += $zprice;
            $leader->zmoney += $zprice;
            $leader->save();
            SyMsg::create([
                'u_id' => $leader->id,
                'msg' => '用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $zprice
            ]);
            //添加销售收益
            $xsuser = User::where('user_type', 2)->find($leader->p_id);
            if ($xsuser) {
                $xsuser->money += $order['price'] * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->zmoney += $order['price'] * XsUser::whereUId($xsuser->id)->first()['tc'];
                $xsuser->save();
                SyMsg::create([
                    'u_id' => $xsuser->id,
                    'msg' => '社区代理' . $leader->nickname . '用户' . User::find($order->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $order['price'] * XsUser::whereUId($xsuser->id)->first()['tc']
                ]);
            }
        }

        //提货点收益
        $thd = User::where('user_type', 3)->find(Group::find($order->group_id)->u_id);
        $psorder = DB::table('log_ps')->where('id',PsList::whereOrderId($order->id)->first()->log_ps_id)->sharedLock()->first();
        if ($psorder->is_js) {
            return 200;
        }
        DB::table('log_ps')->where('id',PsList::whereOrderId($order->id)->first()->log_ps_id)->update([
            'is_js' => 1
        ]);
        if ($thd) {
            $thd->money += $psorder->price;
            $thd->zmoney +=  $psorder->price;
            $thd->save();
            SyMsg::create([
                'u_id' => $thd->id,
                'msg' => '提货点收益-用户' . User::find($order->u_id)->nickname . '购买产品',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $psorder->price
            ]);
        }
        return 200;
    }


    public function getMySy(Request $request)
    {
        $list = SyMsg::whereUId($request['userinfo']['id'])->orderBy('created_at','desc')->get();
        return $this->response->array([
            'data' => $list,
            'user' => [
                'zmoney' => $request['userinfo']['zmoney'],
                'money' => $request['userinfo']['money'],
            ],
            'user_type' => $request['userinfo']['user_type'],
            'week' => SyMsg::whereUId($request['userinfo']['id'])->whereBetween('created_at', [date('Y-m-d H:i:s', time() - 86400 * 7), date('Y-m-d H:i:s', time())])->sum('sy'),
            'is_merchant' => $request['userinfo']['is_merchant']
        ]);
    }

    public function getMyDjs(Request $request)
    {
        $list = Order::whereLeaderId($request['userinfo']['id'])->whereIn('status',[1,2,3])->orderBy('created_at','desc')->get();
        $list = $list->map(function ($v){
            return [
                'msg' => '购买收益-'.User::find($v->u_id)->nickname,
                'sy'  => round(Product::find($v->items[0]->goods_id)->leader_sy * $v->items[0]->num,2),
                'time' => $v->paid_at
            ];
        });

        $thd = Group::whereUId($request['userinfo']['id'])->first();

        if ($thd) {
            //提货点收益
            $thdsy = LogPs::wherehas('items.order',function ($query )use ($thd){
                $query->where('group_id',$thd->id)->whereIn('status',[1,2,3]);
            })->orderBy('created_at','desc')->get();
            $thdsy = $thdsy->map(function ($v){
                return [
                    'msg' => '提货点收益',
                    'sy'  => $v->price,
                    'time' => $v->created_at->format('Y-m-d H:i:s')
                ];
            });
            $collection = collect([$thdsy,$list]);

            $collapsed = $collection->collapse();

            $list = $collapsed->all();
        }

        return $this->response->array([
            'data' => $list,
            'user' => [
                'zmoney' => $request['userinfo']['zmoney'],
                'money' => $request['userinfo']['money'],
            ],
            'user_type' => $request['userinfo']['user_type'],
            'week' => SyMsg::whereUId($request['userinfo']['id'])->whereBetween('created_at', [date('Y-m-d H:i:s', time() - 86400 * 7), date('Y-m-d H:i:s', time())])->sum('sy'),
            'is_merchant' => $request['userinfo']['is_merchant']
        ]);
    }

    public function tx(Request $request)
    {
        if (!$request->name || !$request->tel|| !$request->banknum|| !$request->khh|| !$request->khzh) {
            return 400;
        }

        if ($request->price < 10) {
            return 404;
        }
        DB::beginTransaction();
        $user = DB::table('users')->lockForUpdate()->find($request['userinfo']['id']);
        if ($user->money - $request->price < 0) {
            return 400;
        }
        DB::table('users')->where('id',$user->id)->where('money','>=',$request->price)->update([
            'money' => $user->money - $request->price
        ]);
        TxMsg::create([
            'u_id' => $user->id,
            'price' => $request->price,
            'name' => $request->name,
            'tel' => $request->tel,
            'banknum' => $request->banknum,
            'khh' => $request->khh,
            'khzh' => $request->khzh
        ]);
        DB::commit();
        return 200;
    }

    public function getMyTeamByOpenid(Request $request)
    {

        switch ($request->time) {
            case '昨天':
                $list = User::where('p_id', $request['userinfo']['id'])
                    ->where('user_type', $request->type)
                    ->orderBy('zmoney','desc')
                    ->orderBy('created_at','desc')
                    ->WhereBetween('created_at', [date('Y-m-d',time()-24*60*60),date('Y-m-d',time())])
                    ->get(['nickname', 'avatar', 'created_at', 'zmoney']);
                break;
            case '最近一周':
                $list = User::where('p_id', $request['userinfo']['id'])
                    ->where('user_type', $request->type)
                    ->orderBy('zmoney','desc')
                    ->orderBy('created_at','desc')
                    ->WhereBetween('created_at', [date('Y-m-d',time()-24*60*60*7),date('Y-m-d H:i:s',time())])
                    ->get(['nickname', 'avatar', 'created_at', 'zmoney']);
                break;
            case '最近一个月':
                $list = User::where('p_id', $request['userinfo']['id'])
                    ->where('user_type', $request->type)
                    ->orderBy('zmoney','desc')
                    ->orderBy('created_at','desc')
                    ->WhereBetween('created_at', [date('Y-m-d',time()-24*60*60*30),date('Y-m-d H:i:s',time())])
                    ->get(['nickname', 'avatar', 'created_at', 'zmoney']);
                break;
            case '最近三个月':
                $list = User::where('p_id', $request['userinfo']['id'])
                    ->where('user_type', $request->type)
                    ->orderBy('zmoney','desc')
                    ->orderBy('created_at','desc')
                    ->WhereBetween('created_at', [date('Y-m-d',time()-24*60*60*90),date('Y-m-d H:i:s',time())])
                    ->get(['nickname', 'avatar', 'created_at', 'zmoney']);
                break;
        }

        ;
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function scsfz(Request $request)
    {
        $rew = Sfz::create([
            'u_id' => $request['userinfo']['id'],
            'sfzZ' => $request->sfzZ,
            'sfzF' => $request->sfzF,
            'sfzSC' => $request->sfzSC
        ]);
        return $this->response->array([
            'data' => $rew
        ]);
    }


    public function getUserInfoByid(Request $request)
    {
        $user = User::find($request->id);
        return $this->response->array([
            'data' => $user->openid
        ]);
    }


    public function addformid(Request $request)
    {
        FormId::create([
            'u_id' => $request['userinfo']['id'],
            'formid' => $request->formid
        ]);
    }


    public function getmythlistbyq(Request $request)
    {
        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        $list = Order::whereHas('items.goods', function ($query)use ($request) {
             $query->where('title', 'like', '%'.$request->q.'%');
         })->whereStatus(2)->whereGroupId($group_id)->get();
//        dd($list);
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'sn' => $v->sn,
                'ps_time' => $v->ps_time,
                'items' => $v->items->map(function ($vi) {
                    return [
                        'id' => $vi->goods['id'],
                        'title' => $vi->goods['title'],
                        'thumb' => env('APP_URL') . '/uploads/' . $vi->goods['pics'][0],
                        'num' => $vi->num,
                        'group_sy' => $vi->goods->group_sy
                    ];
                }),
                'name' => $v->name,
                'tel' => $v->tel,
                'status' => $v->status,
                'is_check' => false,
                'statusname' => $this->statusname($v->status)
            ];
        });
        return $this->response->array([
            'list' => $list
        ]);
    }

    public function pldh(Request $request)
    {
        $group_id = Group::whereUId($request['userinfo']['id'])->first()->id;
        foreach ($request->list as $v) {
            $order = Order::whereId($v)->whereGroupId($group_id)->first();
            $order->status = 3;
            $order->save();
            Msg::sendmsg(2, $order->tel);
            $psorder = PsOrder::whereOrderId($order->id)->first();
            $psorder->status = 1;
            $psorder->save();
        }
        return $this->response()->array([
            'code' => 200
        ]);
    }

    /**
     * 延时收货
     * @return int
     */
    public function ysshbyid(Request $request)
    {
        $order = Order::whereUId($request['userinfo']['id'])->find($request->id);
        if ($order->is_ys) {
            return 400;
        }
        $order->is_ys = true;
        $order->save();
        $this->dispatch(new YsShOrder($order, 60 * 60 * 24 * 14));
        return 200;
    }

    /**
     * 延时收货
     * @return int
     */
    public function ysshbyidaddthd(Request $request)
    {
        $order = Order::whereUId($request['userinfo']['id'])->find($request->id);
        if ($order->is_ys) {
            return 400;
        }
        $order->is_ys = true;
        $order->save();
        $this->dispatch(new YsZdshOrder($order, 60 * 60 * 24 * 14));
        return 200;
    }


    /**
     * 取消订单
     * @return int
     */
    public function cancelbyid(Request $request)
    {
        $order = Order::whereUId($request['userinfo']['id'])->find($request->id);

        if ($order->status != 0) {
            return 400;
        }
        if ($order->closed == 1) {
            $order->status = -1;
            $order->save();
        } else {
            $yhq = UserCoupon::find($order->coupon_id);
            if ($yhq) {
                $yhq->is_used = false;
                $yhq->save();
                $price = CouponCode::find($yhq->coupon_id)->getfAdjustedPrice($order->price);
            } else {
                $price = $order->price;
            }
            $order->status = -1;
            $order->coupon_id = 0;
            $order->price = $price;
            $order->save();
        }
        return 200;
    }

    public function getmytxmsg(Request $request)
    {
        $list = TxMsg::where('u_id',$request['userinfo']['id'])->get();
        $list = $list->map(function ($v){
           return [
               'price' => $v->price,
               'time' => $v->created_at->format('Y-m-d H:i:s'),
               'status' => !$v->is_pass? '审核中' : '已提现'
           ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }
}
