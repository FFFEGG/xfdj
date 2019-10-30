<?php

namespace App\Logistic\Actions\Ps;

use App\Group;
use App\LogPerson;
use App\LogPs;
use App\Msg;
use App\Order;
use App\PsList;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class Hbdy extends BatchAction
{
    public $name = '合并打印';
    protected $selector = '.report-dy';

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
        }
        $str = '';
        foreach ($collection as $model) {
            $order = Order::find($model->id);
            $str.= $order->items[0]->goods->title.'X'.$order->items[0]->num.';';
        }
        $str = substr($str,0,-1);

        $orderInfo = '<C><DB>幸福家家</DB></C><BR>';
        $orderInfo .= '订单号:'.$order->sn.'<BR>';
        $orderInfo .= '团购结束时间:'.$order->items[0]->goods->end_time.'<BR>';
        $orderInfo .= '取货点:'.Group::find($order->group_id)->title.'<BR>';
        $orderInfo .= '取货点地址:'.Group::find($order->group_id)->address.'<BR>';
        $orderInfo .= '<C>-----------取件信息-------------</C><BR>';
        $orderInfo .= '<L>产品：'.$str.'</L><BR>';
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
            return $this->response()->success('操作成功！')->refresh();
        }
    }


    public function html()
    {
        return "<a class='report-dy btn btn-sm '><i class='fa fa-camera'></i>合并打印</a>";
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

}
