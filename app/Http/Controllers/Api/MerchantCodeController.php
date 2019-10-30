<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Merchant;
use App\Product;
use App\ShCart;
use App\ShKc;
use App\ShOrder;
use App\ShOrderMsg;
use App\ShPsOrder;
use App\ShSmOrder;
use App\User;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;

class MerchantCodeController extends Controller
{
    public function __construct()
    {
        $config = [
            // 必要配置
            'app_id' => 'wxed65c3911947e645',
            'mch_id' => '1533565191',
            'secret'=> '6bac2b4155da598e2ba423a17b6ad471',
            'key' => 'KcacN4pVHn0VGWDzBy5INysCSmH07q1M',   // API 密钥
            'notify_url' => env('APP_URL') . '/api/merchant_notify',     // 你也可以在下单时单独设置来想覆盖它
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
                'file' => __DIR__.'/wechat.log',
            ],
        ];

        $this->officialAccount = Factory::officialAccount($configs);
        $this->miniProgram = Factory::miniProgram($configs);
    }

    public function addcarbysh(Request $request)
    {
        $cart = ShCart::whereUId($request['userinfo']['id'])->whereGoodsId($request->id)->first();

        if ($cart) {
            $cart->num++;
            $cart->save();
        } else {
            ShCart::create([
                'u_id' => $request['userinfo']['id'],
                'goods_id' => $request->id,
                'num' => 1
            ]);
        }
        return 200;
    }

    public function getCartList(Request $request)
    {
        $list = ShCart::whereUId($request['userinfo']['id'])->get();
        $list = $list->map(function ($v){
           return [
               'id' => $v->id,
               'goods_id' => $v->goods_id,
               'num' => $v->num,
               'thumb' => env('APP_URL').'/uploads/'.Product::find($v->goods_id)->pics[0],
               'title' => Product::find($v->goods_id)->title,
               'price' => Product::find($v->goods_id)->price,
               'style' => ''
           ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }

    public function delShcart(Request $request)
    {
        ShCart::whereUId($request['userinfo']['id'])->whereId($request->id)->delete();
        return 200;
    }

    public function getShInfo(Request $request)
    {
        $user = Merchant::whereUId($request['userinfo']['id'])->first();
        return $this->response->array([
            'data' => $user
        ]);
    }


    public function applyPurchase(Request $request)
    {
        $user = Merchant::whereUId($request['userinfo']['id'])->first();
        $rew = ShOrder::create([
            'u_id' => $request['userinfo']['id'],
            'name' => $user->name,
            'tel' => $user->tel,
            'status' => 0,
            'address' => $user->address,
        ]);
        foreach ($request->list as $v) {
            ShOrderMsg::create([
                'order_id' => $rew['id'],
                'goods_id' => $v['goods_id'],
                'num' => $v['num'],
                'price' => $v['price']
            ]);
        }
        ShCart::whereUId($request['userinfo']['id'])->delete();
        return $this->response->array([
            'code' => 200,
            'order' => $rew
        ]);
    }


    public function getShorderList(Request $request)
    {
        $list = ShOrder::with(['msg','msg.goods'])->orderBy('created_at','desc')->whereUId($request['userinfo']['id'])->paginate(15);
        $list = $list->map(function ($v){
           return [
               'id' => $v->id,
               'time' => $v->created_at->format('Y年m月d日 H:i:s'),
               'status' => $this->statusSh($v->status),
               'goods' => $v->msg->map(function ($vi){
                 return [
                   'thumb' => env('APP_URL').'/uploads/'.$vi->goods->pics[0],
                   'title' => $vi->goods->title,
                   'num' => $vi->num
                 ];
               })

           ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function statusSh($status)
    {
        switch ($status) {
            case 0:
                return '审核中';
                break;
            case 1:
                return '审核通过，待发货';
                break;
            case 2:
                return '审核通过，发货中';
                break;
            case 3:
                return '审核通过，已到货';
                break;
        }
    }

    public function getMyKc(Request $request)
    {
        $data = ShKc::where('u_id',$request['userinfo']['id'])->with('goods')->orderBy('updated_at','desc')->paginate(4);
        $data = $data->map(function ($v){
            return [
                'id' => $v->id,
                'price' => $v->goods->price,
                'thumb' => env('APP_URL') . '/uploads/' . $v->goods->pics[0],
                'title' => $v->goods->title,
                'old_price' => $v->goods->old_price,
                'stock' => $v->stock,
                'num' => 1
            ];
        });
        return $this->response->array([
            'data' => $data
        ]);
    }

    public function geyspm(Request $request)
    {
        $sence = 'id='.$request->data['id'].'&num='.$request->data['num'];
        $response = $this->miniProgram->app_code->getUnlimit($sence, [
            'page'  => 'merchant/shgoods',
            'width' => 600,
        ]);

        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->save('suncode','id='.$request->data['id'].'&num='.$request->data['num'].'.png');
        }
        return env('APP_URL').'/suncode/'.$filename;
    }

    public function smzf(Request $request)
    {
        $data = ShKc::with('goods')->find($request->id);
        return $this->response->array([
            'title' => $data->goods->title,
            'thumb' => env('APP_URL').'/uploads/'.$data->goods->pics[0],
            'price' => $data->goods->price,
            'num' => $request->num,
            'u_id' => $data->u_id,
            'content' =>  $data->goods->content,
            'zprice' => ($data->goods->price  * 100 * intval($request->num) * 100) /10000
        ]);
    }



    public function createShOrder(Request $request)
    {
        if (ShKc::find($request->sh_goods_id)->goods->is_xg) {
            $order = ShSmOrder::whereUId($request['userinfo']['id'])->where('sh_goods_id',$request->sh_goods_id)
                ->where('status',1)
                ->count('num');
            if ($order >= ShKc::find($request->sh_goods_id)->goods->xg_num || $request->num > ShKc::find($request->sh_goods_id)->goods->xg_num) {
                return 400;
            }
        }

        $sn = time().rand(1000,9999);
        $order = ShSmOrder::create([
            'u_id' => $request['userinfo']['id'],
            'sh_goods_id' => $request->sh_goods_id,
            'num' => $request->num,
            'price' => ShKc::find($request->sh_goods_id)->goods->price * $request->num,
            'sn' => $sn
        ]);
        $sh = User::find(ShKc::find($request->sh_goods_id)->u_id);
        if ($sh->user_type == 2 && $sh->is_merchant == 1) {
            $cj = true;
        } else {
            $cj = false;
        }

        $product = Product::find(ShKc::find($request->sh_goods_id)->goods_id);


        $result = $this->app->order->unify([
            'body' => '幸福到家-' . $sn,
            'out_trade_no' => $sn,
            'total_fee' => $product['price'] * 100 * $request->num,
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
                'cj' => $cj
            ]);
        } else {
            return $result;
        }
    }
}
