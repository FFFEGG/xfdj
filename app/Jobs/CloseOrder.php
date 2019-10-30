<?php

namespace App\Jobs;

use App\CouponCode;
use App\UserCoupon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Order;
use Illuminate\Support\Facades\Log;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseOrder implements ShouldQueue
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
        if ($this->order->paid_at || $this->order->status == -1) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $yhq = UserCoupon::find($this->order->coupon_id);
            if ($yhq) {
                $yhq->is_used = false;
                $yhq->save();
                $price = round($this->order->items[0]->price * $this->order->items[0]->num,2);
            } else {
                $price = $this->order->price;
            }
            $this->order->update(['closed' => 1,'coupon_id' => 0,'price'=>$price]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
            foreach ($this->order->items as $item) {
                $item->goods->addStock($item->num);
            }
        });
    }
}
