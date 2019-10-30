<?php

namespace App\Logistic\Actions\Ps;

use App\LogPerson;
use App\LogPs;
use App\Msg;
use App\Order;
use App\PsList;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchReplicate extends BatchAction
{
    public $name = '批量配送';

    protected $selector = '.report-posts';

    public function handle(Collection $collection, Request $request)
    {
        $u_id = 0;
        foreach ($collection as $k=>$model) {
            if ($k == 0) {
                $u_id = $model->u_id;
            } else {
                if ($u_id != $model->u_id) {
                    return $this->response()->error('用户不一致无法合并！')->refresh();
                }
            }
            if ($model->status != 1) {
                return $this->response()->error('存在已配送的订单！')->refresh();
            }
        }
        $rew = LogPs::create([
            'u_id' => $request->u_id,
            'price' => $request->price,
            'status' => 0,
            'is_js' => 0,
        ]);
        $log_ps_id = $rew->id;
        foreach ($collection as $model) {
            //
            PsList::create([
                'log_ps_id' => $log_ps_id,
                'order_id' => $model->id,
                'u_id' => $model->u_id,
            ]);
            $order = Order::find($model->id);
            $order->status = 2;
            $order->ps_time = date('Y-m-d H:i:s');
            $order->save();
            //发送短信
            Msg::sendmsg(1,$order->tel,$order->items[0]->goods->title);
        }

        return $this->response()->success('操作成功！')->refresh();
    }

    public function form()
    {
        $user = LogPerson::get();
        foreach ($user as $v){
            $arr[$v->id] = $v->name;
        }
        $this->select('u_id', '物流人员')->options($arr)->rules('required');
        $this->text('price', '提货点收益')->rules('required|size:1');
    }

    public function html()
    {
        return "<a class='report-posts btn btn-sm btn-danger'><i class='fa fa-instagram'></i>合并配送</a>";
    }


}
