<?php


namespace App\Http\Controllers;


use App\Active;
use Illuminate\Http\Request;

class ActiveController extends Controller
{
    public function CommunityAgent(Request $request)
    {
        if ($request->isMethod('post')) {
            if (Active::whereTel($request->activetel)->first()) {
                return back()->with('success','您已提交申请，请勿重新提交');
            }
            $this->validate($request,[
                'activename' => 'required',
                'activetel' => 'required|min:11|max:11',
            ]);

            $rew = Active::create([
                'name' => $request->activename,
                'tel' => $request->activetel,
                'sqdl' => 1,
            ]);

            if ($rew) {
                return back()->with('success','申请成功');
            }
        }

        return view('active.CommunityAgent');
    }

    public function PickUpPoint(Request $request)
    {
        if ($request->isMethod('post')) {
            // 要执行的代码
            if (Active::whereTel($request->activetel)->first()) {
                return back()->with('success','您已提交申请，请勿重新提交');
            }
            $this->validate($request,[
                'activename' => 'required',
                'activetel' => 'required|min:11|max:11',
            ]);

            $rew = Active::create([
                'name' => $request->activename,
                'tel' => $request->activetel,
                'qhd' => $request->qhd == 'on'?1:0,
                'sqsh' => $request->sqsh == 'on'?1:0,
            ]);

            if ($rew) {
                return back()->with('success','申请成功');
            }
        }

        return view('active.PickUpPoint');
    }

    public function Suppliergys(Request $request)
    {
        if ($request->isMethod('post')) {
            // 要执行的代码
            if (Active::whereTel($request->activetel)->first()) {
                return back()->with('success','您已提交申请，请勿重新提交');
            }
            $this->validate($request,[
                'activename' => 'required',
                'activetel' => 'required|min:11|max:11',
            ]);

            $rew = Active::create([
                'name' => $request->activename,
                'tel' => $request->activetel,
                'gys' => 1,
            ]);

            if ($rew) {
                return back()->with('success','申请成功');
            }
        }

        return view('active.Suppliergys');
    }
}
