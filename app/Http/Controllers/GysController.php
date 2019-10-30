<?php


namespace App\Http\Controllers;
use App\Goods;
use App\GoodsCate;
use App\Gys;
use App\GysType;
use App\TgEndOrder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GysController extends Controller
{
    public function supplier_register()
    {

        $list = GysType::get();
        return view('supplier.register',compact('list'));
    }


    public function register_gys(Request $request)
    {
        $imgs = '';
        if ($request->file('file')) {
            foreach ($request->file('file') as $file) {
                $path = $file->store('public/images');
                $url = Storage::url($path);
                $imgs .= $url.',';
            }
        }
        $hyzz = '';
        if ($request->file('hyzz')) {
            foreach ($request->file('hyzz') as $file) {
                $path = $file->store('public/images');
                $url = Storage::url($path);
                $hyzz .= $url.',';
            }
        }
        sleep(2);
        $this->validate($request, [
            'username' => 'required|unique:gys',
            'password' => 'required|confirmed|min:6',
            'name' => 'required',
            'tel' => 'required|min:11|max:11',
            'file' => 'required',
            'hyzz' => 'required',
            'hy_type' => 'required',
            'hy_type_value' => 'required',
        ]);

        $rew = Gys::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'name' => $request->name,
            'tel' => $request->tel,
            'type' => $request->type,
            'file' => substr($imgs,0,-1),
            'hyzz' => substr($hyzz,0,-1),
            'hy_type' => $request->hy_type,
            'hy_type_value' => $request->hy_type_value,
        ]);

        return redirect('/supplier_login')->with('success','资料已提交，等待审核');
    }

    public function supplier_login()
    {
        return view('supplier.login');
    }

    public function supplier_login_store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);
        $user = Gys::where('username',$request->username)->first();
        if (!$user) {
            return back()->with('error','账户密码错误');
        }

        if ($user->status == 0) {
            return back()->with('error','账户审核中');
        }

        if ($user->status == -1) {
            return back()->with('error','账户已冻结');
        }

        if (Hash::check($request->password,$user->password)) {
            $request->session()->put('gys',$user);
            return redirect('/supplier/index');
        } else {
            return back()->with('error','账户密码错误');
        }
        return;
    }

    public function index(Request $request)
    {
        $active = 1;
        return view('supplier.index',compact('active'));
    }


    public function goodsuploads(Request $request)
    {
        $active = 4;

        $goodstype = GoodsCate::get();

        if($request->isMethod('post')){

            $imgs = '';
            if ($request->file('file')) {
                foreach ($request->file('file') as $file) {
                    $path = $file->store('public/images');
                    $url = Storage::url($path);
                    $imgs .= $url.',';
                }
            }

            // 要执行的代码
            $this->validate($request,[
               'title' => 'required',
               'file' => 'required',
            ]);

            $rew = Goods::create([
                'gys_id' => $request->session()->get('gys')->id,
                'title' => $request->title,
                'gys_price' => 0,
                'type' => $request->type,
                'cgy' => $request->cgy,
                'image' => substr($imgs,0,-1)
            ]);
            if($rew) {
                return back()->with('success','上传成功');
            }
        }
        return view('supplier.goodsuploads',compact('active','goodstype'));
    }

    public function edit(Request $request)
    {
        $active = 4;

        $goodstype = GoodsCate::get();

        $goods = Goods::find($request->id);


        if($request->isMethod('post')){

            $imgs = '';
            if ($request->file('file')) {
                foreach ($request->file('file') as $file) {
                    $path = $file->store('public/images');
                    $url = Storage::url($path);
                    $imgs .= $url.',';
                }
            }

            // 要执行的代码
            $this->validate($request,[
                'title' => 'required',
                'file' => 'required',
            ]);

            $rew = Goods::where('id',$request->id)->update([
                'gys_id' => $request->session()->get('gys')->id,
                'title' => $request->title,
                'gys_price' => 0,
                'type' => $request->type,
                'image' => substr($imgs,0,-1)
            ]);
            if($rew) {
                return back()->with('success','上传成功');
            }
        }

        return view('supplier.edit',compact('active','goodstype','goods'));
    }


    public function goodslist(Request $request)
    {

        $active = 3;

        $list = Goods::whereGysId($request->session()->get('gys')->id)->paginate(20);

        return view('supplier.goodslist',compact('active', 'list'));
    }

    public function Industry()
    {
        return view('supplier.word');
    }

    public function orders(Request $request)
    {
        $active = 2;
        $list = TgEndOrder::where('u_id',$request->session()->get('gys')->id)
            ->where('status',1)
            ->orderBy('created_at','desc')
            ->paginate(20);
        return view('supplier.orders',compact('active','list'));
    }
}
