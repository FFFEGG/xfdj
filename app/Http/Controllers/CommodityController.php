<?php


namespace App\Http\Controllers;


use App\Commodity;
use App\Goods;
use App\Gys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CommodityController extends Controller
{
    public function login()
    {
        return view('commodity.login');
    }

    public function commodity_store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);
        $user = Commodity::where('username',$request->username)->first();
        if (!$user) {
            return back()->with('error','账户密码错误');
        }


        if ($user->status == 0) {
            return back()->with('error','账户审核中');
        }


        if (Hash::check($request->password,$user->password)) {
            $request->session()->put('comm',$user);
            return redirect('/commodity/index');
        } else {
            return back()->with('error','账户密码错误');
        }
    }

    public function index()
    {
        $active = 1;
        return view('commodity.index',compact('active'));
    }

    public function supplier_status()
    {
        $active = 2;

        $list = Gys::orderBy('created_at','desc')->paginate(20);
        return view('commodity.supplier_status',compact('active','list'));
    }

    public function gysedit(Request $request,Gys $gys)
    {

        $active = 2;
        if ($request->isMethod('post')) {
            $gys->status = $request->status;
            $gys->save();
            return redirect('/commodity/supplier_status')->with('success','操作成功');
        }
        return view('commodity.gysedit',compact('active','gys'));
    }


    /**
     *  供应商产品列表
     */
    public function gysgoodslist(Request $request)
    {
        $active = 2;
        $name = Gys::find($request->id)['name'];

        $list = Goods::whereGysId($request->id)->paginate(20);

        return view('commodity.gysgoodslist',compact('active','name','list'));
    }

    /**
     * 供应商产品状态修改
     * @param Request $request
     */
    public function goods_status(Request $request)
    {
        $goods = Goods::find($request->id);
        $goods->is_pass = $request->is_pass;
        $goods->save();
        return back()->with('success','操作成功');
    }
}
