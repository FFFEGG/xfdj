<?php


namespace App\Http\Controllers\Api;


use App\FormId;
use App\Group;
use App\Http\Controllers\Controller;
use App\Merchant;
use App\Order;
use App\OrderMsg;
use App\Product;
use App\ShKc;
use App\ShSmOrder;
use App\SyMsg;
use App\TgEndOrder;
use App\User;
use App\Wechat;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class wechatController extends Controller
{

    public function __construct()
    {
        $config = [
            // 必要配置
            'app_id'             => 'wxed65c3911947e645',
            'mch_id'             => '1533565191',
            'key'                => 'KcacN4pVHn0VGWDzBy5INysCSmH07q1M',   // API 密钥
            'notify_url'         => env('APP_URL').'/api/wecaht_notify',     // 你也可以在下单时单独设置来想覆盖它
        ];

        $this->app = Factory::payment($config);
    }

    public function wecaht_notify(Request $request)
    {

        $response = $this->app->handlePaidNotify(function($message, $fail){
//            Log::info('支付回调参数：', $message);
//            Log::info('$message'.$message);
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::whereSn($message['out_trade_no'])->get();

            foreach ($order as $v) {
                if (!$v || $v->status == 1) { // 如果订单不存在 或者 订单已经支付过了
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    foreach ($order as $k=>$v) {
                        $order[$k]->paid_at = date('Y-m-d H:i:s',time()); // 更新支付时间为当前时间
                        $order[$k]->status = 1;
                        $order[$k]->closed = 1;
                        $order[$k]->payment_method = 'wechat';
                        $order[$k]->payment_no = $message['transaction_id'];
                        foreach (OrderMsg::whereOrderId($order[$k]['id'])->get() as $item) {
                            //添加团购结束信息
                            $TgEndOrder = TgEndOrder::whereUId($item->goods->gys_id)
                                ->whereGoodsId($item->goods->id)
                                ->whereSpec($item->spec)
                                ->whereEndTime($item->goods->end_time)->first();
                            if ($TgEndOrder) {
                                $TgEndOrder->increment('num', $item->num);
                            } else {
                                TgEndOrder::create([
                                    'u_id' => $item->goods->gys_id,
                                    'goods_id' => $item->goods->id,
                                    'end_time' => $item->goods->end_time,
                                    'spec' => $item->spec,
                                    'num' => $item->num
                                ]);
                            }
                        }
                    }

                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    foreach ($order as $k=>$v) {
                        $order[$k]->status = 0;
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            foreach ($order as $k=>$v) {
                $order[$k]->save();
            }


            return true; // 返回处理完成
        });
        return $response;
//      $response->send(); // return $response;
    }


    public function merchant_notify(Request $request)
    {

        $response = $this->app->handlePaidNotify(function($message, $fail){
//            Log::info('支付回调参数：', $message);
//            Log::info('$message'.$message);
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = ShSmOrder::whereSn($message['out_trade_no'])->first();
//            Log::info('支付回调参数：', $order->toArray());
            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->status = 1;

                    //减少库存
                    $sh_kc = ShKc::find($order->sh_goods_id);
                    $sh_kc->stock =  ($sh_kc->stock - $order->num) <= 0 ? 0 : $sh_kc->stock - $order->num;
                    $sh_kc->save();
                    //增加佣金
                    SyMsg::create([
                        'u_id' => $sh_kc->u_id,
                        'msg' => '扫码购买收益-'.User::find($order->u_id)->nickname,
                        'time' => date('Y-m-d H:i:s', time()),
                        'sy' => $sh_kc->goods->Shsy * $order->num
                    ]);
                    //用户增加佣金
                    $user = User::find($sh_kc->u_id);
                    $user->money = $user->money + $sh_kc->goods->Shsy * $order->num;
                    $user->zmoney = $user->zmoney + $sh_kc->goods->Shsy * $order->num;
                    $user->save();
//                    if (FormId::where('u_id',$user->id)->first()) {
//                        $fromid = FormId::where('u_id',$user->id)->first()->formid;
//                        Wechat::msg()->template_message->send([
//                            'touser' => $user->openid,
//                            'template_id' => 'UUXza60gUgNAM32COVlzrZL8ALFmzME67j5u4X7_f5E',
//                            'page' => '/merchant/index',
//                            'form_id' => $fromid,
//                            'data' => [
//                                'keyword1' => Merchant::whereUId($user->id)->first()->shopname,
//                                'keyword2' => $order->price,
//                                'keyword3' => $order->updated_at,
//                                'keyword4' =>  $sh_kc->goods->title
//                            ],
//                            'emphasis_keyword'=>'keyword2.DATA'
//                        ]);
//                        FormId::where('formid',$fromid)->delete();
//                    }
//                    Wechat::msg()->template_message->send([
//                        'touser' => User::find($order->u_id)['openid'],
//                        'template_id' => 'UUXza60gUgNAM32COVlzrZL8ALFmzME67j5u4X7_f5E',
//                        'page' => '/pages/index',
//                        'form_id' => $order->formid,
//                        'data' => [
//                            'keyword1' => Merchant::whereUId($user->id)->first()->shopname,
//                            'keyword2' => $order->price,
//                            'keyword3' => $order->updated_at,
//                            'keyword4' =>  $sh_kc->goods->title
//                        ],
//                        'emphasis_keyword'=>'keyword2.DATA'
//                    ]);
                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 0;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save();


            return true; // 返回处理完成
        });
        return $response;
    }
}
