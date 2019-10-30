<?php


namespace App\Http\Controllers\Api;


use App\CouponCode;
use App\Http\Controllers\Controller;
use App\User;
use App\UserCoupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function getmyyhq(Request $request)
    {
        $fuser = User::where('openid',$request->u_id)->first();
        if ($fuser) {
            $list = UserCoupon::whereUId($request['userinfo']['id'])->whereHas('yhq',function($query){
                    $query  ->where('not_before', '<=', date('Y-m-d H:i:s'))
                        ->where('not_after', '>=', date('Y-m-d H:i:s'));
                })
                ->whereIn('p_id',[0,$fuser->id])
                ->where('is_used',false)->get();
        } else {
            $list = UserCoupon::whereUId($request['userinfo']['id'])->whereHas('yhq',function($query){
                    $query  ->where('not_before', '<=', date('Y-m-d H:i:s'))
                        ->where('not_after', '>=', date('Y-m-d H:i:s'));
                })
                ->where('is_used',false)
                ->where('p_id',0)
                ->get();
        }



        $list = $list->map(function ($v){
           return [
               'coupon_id' => $v->coupon_id,
               'id' => $v->id,
               'name' => CouponCode::find($v->coupon_id)->name,
               'type' => CouponCode::find($v->coupon_id)->type,
               'value' => CouponCode::find($v->coupon_id)->value,
               'min_amount' => CouponCode::find($v->coupon_id)->min_amount,
               'is_check' => false,
           ];
        });
        return $this->response->array([
            'list' => $list
        ]);

    }
}
