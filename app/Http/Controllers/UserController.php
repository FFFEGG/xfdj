<?php


namespace App\Http\Controllers;


use App\Group;
use App\LeaderZc;
use App\Msg;
use App\MsgCode;
use App\ThdZc;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use QrCode;
class UserController extends Controller
{
    public function leaderRegister(Request $request)
    {
        $user = User::whereOpenid($request->openid)->first();
        $url = env('APP_URL').'/leader?id=' . $user->id;
        $qrcode = QrCode::format('png')
            ->size(300)
            ->margin(0)
            ->errorCorrection('H')
            ->merge($user->avatar, 0.3, true)
            ->generate($url);
        $name = env('APP_URL').'/storage/'.$user->openid.'leader.jpg';
        Storage::put('public/'.$user->openid.'leader.jpg', $qrcode,'public');
        return view('users.show', compact( 'qrcode','user','name'));
    }

    public function addressRegister(Request $request)
    {
        $user = User::whereOpenid($request->openid)->first();
        $url = env('APP_URL').'/address?id=' . $user->id;
        $qrcode = QrCode::format('png')
            ->size(300)
            ->margin(0)
            ->errorCorrection('H')
            ->merge($user->avatar, 0.3, true)
            ->generate($url);
        $name = env('APP_URL').'/storage/'.$user->openid.'address.jpg';
        Storage::put('public/'.$user->openid.'address.jpg', $qrcode,'public');

        return view('users.show', compact( 'qrcode','user','name'));
    }

    public function shRegister(Request $request)
    {
        $user = User::whereOpenid($request->openid)->first();
        $url = env('APP_URL').'/sh?id=' . $user->id;
        $qrcode = QrCode::format('png')
            ->size(300)
            ->margin(0)
            ->errorCorrection('H')
            ->merge($user->avatar, 0.3, true)
            ->generate($url);
        $name = env('APP_URL').'/storage/'.$user->openid.'sh.jpg';
        Storage::put('public/'.$user->openid.'sh.jpg', $qrcode,'public');

        return view('users.show', compact( 'qrcode','user','name'));
    }


    public function showmycode(Request $request)
    {
        $qrcode = QrCode::format('png')
            ->size(300)
            ->margin(0)
            ->errorCorrection('H')
            ->merge($request['userinfo']['avatar'], 0.3, true)
            ->generate($request->sn);

        return view('users.showmycode', compact( 'qrcode'));
    }

    public function leader_pass(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 1;
        $user->save();

        $msg = LeaderZc::find($request->msg_id);
        $msg->is_sh = 1;
        $msg->save();
        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }


    public function mark_leader_pass(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 1;
        $user->save();

        $msg = LeaderZc::find($request->msg_id);
        $msg->is_sh = 1;
        $msg->save();
        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }

    public function leader_close(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 0;
        $user->save();
        $msg = LeaderZc::find($request->msg_id);
        $msg->is_sh = -1;
        $msg->save();

        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }

    public function mark_leader_close(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 0;
        $user->save();
        $msg = LeaderZc::find($request->msg_id);
        $msg->is_sh = -1;
        $msg->save();

        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }


    public function thd_pass(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 1;
        $user->save();

        $msg = ThdZc::find($request->msg_id);
        $msg->is_sh = 1;
        $msg->save();

        $group = Group::create([
            'u_id' => $user->id,
            'title' => $msg->shopname,
            'address'=> $msg->address,
            'xqname' => $msg->xqname,
            'name'=>$msg->name,
            'tel'=>$msg->tel,
            'sfzz'=>$msg->sfzz,
            'sfzf'=>$msg->sfzf,
            'sfzsc'=>$msg->sfzsc,
            'yyzz'=>$msg->yyzz,
        ]);
        $success = new MessageBag([
            'title'   => '操作成功',
            'message' => '请添加相对于的坐标位置'
        ]);

        Msg::sendmsg(5,$msg->tel);
        return redirect('/admin/group/'.$group->id.'/edit')->with(compact('success'));
    }

    public function mark_thd_pass(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 1;
        $user->save();

        $msg = ThdZc::find($request->msg_id);
        $msg->is_sh = 1;
        $msg->save();

        $group = Group::create([
            'u_id' => $user->id,
            'title' => $msg->shopname,
            'address'=> $msg->address,
            'xqname' => $msg->xqname,
            'name'=>$msg->name,
            'tel'=>$msg->tel,
            'sfzz'=>$msg->sfzz,
            'sfzf'=>$msg->sfzf,
            'sfzsc'=>$msg->sfzsc,
            'yyzz'=>$msg->yyzz,
        ]);
        Msg::sendmsg(5,$msg->tel);
        $success = new MessageBag([
            'title'   => '操作成功',
            'message' => '请添加相对于的坐标位置'
        ]);
        return redirect('/marketing/group/'.$group->id.'/edit')->with(compact('success'));
    }

    public function thd_close(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 0;
        $user->save();
        $msg = ThdZc::find($request->msg_id);
        $msg->is_sh = -1;
        $msg->save();

        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }

    public function mark_thd_close(Request $request)
    {
        $user = User::find($request->id);
        $user->is_sh = 0;
        $user->save();
        $msg = ThdZc::find($request->msg_id);
        $msg->is_sh = -1;
        $msg->save();

        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
    }

}
