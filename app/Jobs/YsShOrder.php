<?php

namespace App\Jobs;

use App\Group;
use App\Msg;
use App\OrderMsg;
use App\Product;
use App\SyMsg;
use App\User;
use App\XsUser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Order;
use Illuminate\Support\Facades\Log;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class YsShOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->status == 4) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['status' => 4]);
            $str = '';
            $zprice = 0;//社区代理收益
            $thdzprice = 0;//提货点收益
            foreach (OrderMsg::with('goods')->where('order_id', $this->order->id)->get() as $v) {
                $str .= Product::find($v->goods_id)['title'] . '; ';
                if ($v['goods']['sy_type'] == 0) {
                    $zprice += $v['goods']['leader_sy'] * $v['goods']['price'] * $v['num'];
                } else {
                    $zprice += $v['goods']['leader_sy'] * $v['num'];
                }
                $thdzprice += $v['goods']['group_sy'];
            }

            Msg::sendmsg(3, $this->order->tel, $str);
            //添加社区代理佣金
            $leader = User::where('is_sh', 1)->where('user_type','!=',0)->find($this->order->leader_id);
            if ($leader) {
                $leader->money += $zprice;
                $leader->zmoney += $zprice;
                $leader->save();
                SyMsg::create([
                    'u_id' => $leader->id,
                    'msg' => '用户' . User::find($this->order->u_id)->nickname . '购买产品',
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
                        'msg' => '社区代理' . $leader->nickname . '用户' . User::find($this->order->u_id)->nickname . '购买产品',
                        'time' => date('Y-m-d H:i:s', time()),
                        'sy' => $zprice * XsUser::whereUId($xsuser->id)->first()['tc']
                    ]);
                }
            }
            //提货点收益
            $thd = User::where('user_type', 3)->find(Group::find($this->order->group_id)->u_id);
            if ($thd) {
                $thd->money += $thdzprice;
                $thd->zmoney += $thdzprice;
                $thd->save();
                SyMsg::create([
                    'u_id' => $thd->id,
                    'msg' => '用户' . User::find($this->order->u_id)->nickname . '购买产品',
                    'time' => date('Y-m-d H:i:s', time()),
                    'sy' => $thdzprice
                ]);
            }
        });
    }
}
