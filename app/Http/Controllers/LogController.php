<?php


namespace App\Http\Controllers;


use App\Group;
use App\Jobs\AutomaticOrder;
use App\Jobs\ZdshOrder;
use App\LogPerson;
use App\LogPs;
use App\Msg;
use App\PsList;
use App\PsOrder;
use App\ShKc;
use App\ShPsOrder;
use App\SyMsg;
use App\User;
use App\XsUser;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function log_login(Request $request)
    {
        if ($request->isMethod('post')) {
            $person = LogPerson::wherePwd($request->pwd)->first();
            if (!$person) {
                return back()->with('error', '口令错误');
            }
            $request->session()->put('ps_person', $person);
            return redirect('/logc/index');
        }
        if ($this->is_weixin()) {
            return view('ps.wx_login');
        } else {
            return view('ps.login');
        }

    }

    function is_weixin()
    {

        if (strpos($_SERVER['HTTP_USER_AGENT'],

                'MicroMessenger') !== false) {

            return true;

        }

        return false;

    }

    public function index(Request $request)
    {
        $active = 1;
        $list = LogPs::where('created_at', '>=', date('Y-m-d', time()))
            ->whereUId($request->session()->get('ps_person')->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $num = LogPs::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();
        return view('ps.index', compact('list', 'active', 'num'));
    }

    public function shorder(Request $request)
    {
        $active = 1;
        $list = ShPsOrder::where('created_at', '>=', date('Y-m-d', time()))
            ->whereUId($request->session()->get('ps_person')->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
//        dd($list);
        $num = ShPsOrder::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();
        return view('shps.index', compact('list', 'active', 'num'));
    }

    public function history(Request $request)
    {
        $active = 2;
        $list = LogPs::whereUId($request->session()->get('ps_person')->id)->orderBy('created_at', 'desc')->paginate(15);
        $num = LogPs::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();

        return view('ps.index', compact('list', 'active', 'num'));
    }

    public function shorderhistory(Request $request)
    {
        $active = 2;
        $list = ShPsOrder::whereUId($request->session()->get('ps_person')->id)->orderBy('created_at', 'desc')->paginate(15);
        $num = ShPsOrder::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();

        return view('shps.index', compact('list', 'active', 'num'));
    }

    public function nops(Request $request)
    {
        $active = 3;
        $list = LogPs::whereUId($request->session()->get('ps_person')->id)
            ->whereStatus(0)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $num = LogPs::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();

        return view('ps.index', compact('list', 'active', 'num'));
    }

    public function shordernops(Request $request)
    {
        $active = 3;
        $list = ShPsOrder::whereUId($request->session()->get('ps_person')->id)
            ->whereStatus(0)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $num = ShPsOrder::whereUId($request->session()->get('ps_person')->id)->whereStatus(0)->count();

        return view('shps.index', compact('list', 'active', 'num'));
    }

    public function psordersd(Request $request)
    {
        $ShPsOrder = $order = ShPsOrder::find($request->id);
        $ShPsOrder->status = 1;
        $ShPsOrder->save();
        $ShPsOrder->order->status = 3;
        $ShPsOrder->order->save();

        //商户添加库存
        $list = $ShPsOrder->order->msg;
        foreach ($list as $v) {
            $kc = ShKc::whereUId($ShPsOrder->order->u_id)->whereGoodsId($v->goods_id)->first();
            if ($kc) {
                $kc->stock+=$v->num;
                $kc->zkc+=$v->num;
                $kc->save();
            } else {
                ShKc::create([
                    'u_id' => $ShPsOrder->order->u_id,
                    'goods_id' => $v->goods_id,
                    'stock' => $v->num,
                    'zkc'=>$v->num
                ]);
            }
        }
        return 200;
    }


    public function shoppsorder(Request $request)
    {
        $order = LogPs::find($request->id);
        $thd = Group::find($order->items[0]->order->group_id);
        $thduser = User::find($thd->u_id);
        $xsuser = User::where('user_type',2)->find($thduser->p_id);

        if ($xsuser) {
            //销售员增加收益
            $xsuser->money += $order->price * XsUser::whereUId($xsuser->id)->first()['tc'];
            $xsuser->zmoney += $order->price  * XsUser::whereUId($xsuser->id)->first()['tc'];
            $xsuser->save();
            SyMsg::create([
                'u_id' => $xsuser->id,
                'msg' => '提货点收益',
                'time' => date('Y-m-d H:i:s', time()),
                'sy' => $order->price * XsUser::whereUId($xsuser->id)->first()['tc']
            ]);
        }
        $order->status = 1;
        $order->save();
        foreach ($order->items as $v) {
            $v->order->status = 3;
            $v->order->save();
            $str = $v->order->items[0]->goods->title;
            Msg::sendmsg(2,  $v->order->tel,$str);
            $this->dispatch(new ZdshOrder($v->order, 60 * 60 * 24 * 7));
        }
        return 200;
    }
}
