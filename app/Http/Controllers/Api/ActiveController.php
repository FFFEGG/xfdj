<?php


namespace App\Http\Controllers\Api;


use App\ActiveGoods;
use App\CouponCode;
use App\DrawRecord;
use App\GoodsDb;
use App\Http\Controllers\Controller;
use App\Img;
use App\Product;
use App\ShSmOrder;
use App\User;
use App\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ActiveController extends Controller
{
    public function getJpList(Request $request)
    {
        $list = ActiveGoods::where('type', 1)->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'name' => $v->name,
                'img' => env('APP_URL') . '/uploads/' . $v->img
            ];
        });

        return $this->response->array([
            'list' => $list,
            'zjlist' => DrawRecord::whereHas('goods', function ($query) {
                $query->where('name', '!=', '下单再来一次');
            })->where('is_zc', 0)->orderBy('created_at', 'desc')->get()->map(function ($v) {
                return [
                    'nickname' => User::whereOpenid($v->openid)->first()->nickname,
                    'goods' => ActiveGoods::find($v->activegoods_id)->name
                ];
            })
        ]);
    }

    public function getZcJpList(Request $request)
    {
        $list = ActiveGoods::where('type', 2)->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'name' => $v->name,
                'img' => env('APP_URL') . '/uploads/' . $v->img
            ];
        });

        return $this->response->array([
            'list' => $list,
            'zjlist' => DrawRecord::whereHas('goods', function ($query) {
                $query->where('name', '!=', '下单再来一次');
            })->orderBy('created_at', 'desc')->where('is_zc', 1)->get()->map(function ($v) {
                return [
                    'nickname' => User::whereOpenid($v->openid)->first()->nickname,
                    'goods' => ActiveGoods::find($v->activegoods_id)->name
                ];
            })
        ]);
    }


    public function LuckDraw(Request $request)
    {
        if ($request->orderid != 0) {
            $order = ShSmOrder::where('u_id', User::where('openid', $request->openid)->first()->id)
                ->where('id', $request->orderid)
                ->where('status', 1)
                ->first();
            if (!$order) {
                return 401;
            }
        }
        //是否有抽奖次数
        $Draw = DrawRecord::where('openid', $request->openid)
            ->where('order_id', $request->orderid)
            ->first();

        if ($Draw) {
            return 400;
        }

        $lists = ActiveGoods::where('type', 1)->get();
        $Drawlist = $lists->map(function ($v) {
            return [
                'id' => $v->id,
                'name' => $v->name,
                'probability' => $v->probability,
                'img' => env('APP_URL') . '/uploads/' . $v->img
            ];
        });

        $list = $lists->map(function ($v) {
            return 10000 * $v->probability;
        });
        $arr = $lists->map(function ($v) {
            return 10000 * $v->probability;
        });
        //几率范围
        $list[1] = $list[0] + $list[1];
        $list[2] = $list[1] + $list[2];
        $list[3] = $list[2] + $list[3];
        $list[4] = $list[4] + $list[3];
        $list[5] = $list[4] + $list[5];
        $list[6] = $list[6] + $list[5];
        $list[7] = $list[6] + $list[7];
        //最大几率
        $num = rand(0, 10000);

        if ($num <= $list[0]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[0]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([16, 24]),
                'data' => $Drawlist[0],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[0] < $num && $num <= $list[1]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[1]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([17, 25]),
                'data' => $Drawlist[1],
                'time' => date('Y-m-d H:i:s', time())
            ]);

        }

        if ($list[1] < $num && $num <= $list[2]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[2]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([10, 18]),
                'data' => $Drawlist[2],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[2] < $num && $num <= $list[3]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[3]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([11, 19]),
                'data' => $Drawlist[3],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[3] < $num && $num <= $list[4]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[4]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([12, 20]),
                'data' => $Drawlist[4],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[4] < $num && $num <= $list[5]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[5]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([13, 21]),
                'data' => $Drawlist[5],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[5] < $num && $num <= $list[6]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[6]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([14, 22]),
                'data' => $Drawlist[6],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[6] < $num && $num <= $list[7]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'order_id' => $request->orderid,
                'activegoods_id' => $lists[7]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([15, 23]),
                'data' => $Drawlist[7],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($num > $list[7]) {
            return 400;
        }
    }

    public function ZcLuckDraw(Request $request)
    {
        //是否有抽奖次数
        $Draw = DrawRecord::where('openid', $request->openid)
            ->where('is_zc', 1)
            ->first();

        if ($Draw) {
            return 400;
        }

        $lists = ActiveGoods::where('type', 2)->get();
        $Drawlist = $lists->map(function ($v) {
            return [
                'id' => $v->id,
                'name' => $v->name,
                'probability' => $v->probability,
                'img' => env('APP_URL') . '/uploads/' . $v->img
            ];
        });

        $list = $lists->map(function ($v) {
            return 10000 * $v->probability;
        });
        $arr = $lists->map(function ($v) {
            return 10000 * $v->probability;
        });
        //几率范围
        $list[1] = $list[0] + $list[1];
        $list[2] = $list[1] + $list[2];
        $list[3] = $list[2] + $list[3];
        $list[4] = $list[4] + $list[3];
        $list[5] = $list[4] + $list[5];
        $list[6] = $list[6] + $list[5];
        $list[7] = $list[6] + $list[7];
        //最大几率
        $num = rand(0, 10000);

        if ($num <= $list[0]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[0]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([16, 24]),
                'data' => $Drawlist[0],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[0] < $num && $num <= $list[1]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[1]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([17, 25]),
                'data' => $Drawlist[1],
                'time' => date('Y-m-d H:i:s', time())
            ]);

        }

        if ($list[1] < $num && $num <= $list[2]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[2]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([10, 18]),
                'data' => $Drawlist[2],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[2] < $num && $num <= $list[3]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[3]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([11, 19]),
                'data' => $Drawlist[3],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[3] < $num && $num <= $list[4]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[4]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([12, 20]),
                'data' => $Drawlist[4],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[4] < $num && $num <= $list[5]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[5]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([13, 21]),
                'data' => $Drawlist[5],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[5] < $num && $num <= $list[6]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[6]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([14, 22]),
                'data' => $Drawlist[6],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($list[6] < $num && $num <= $list[7]) {
            DrawRecord::create([
                'openid' => $request->openid,
                'is_zc' => 1,
                'activegoods_id' => $lists[7]['id']
            ]);
            return $this->response->array([
                'num' => Arr::random([15, 23]),
                'data' => $Drawlist[7],
                'time' => date('Y-m-d H:i:s', time())
            ]);
        }

        if ($num > $list[7]) {
            return 400;
        }
    }

    public function getyhq(Request $request)
    {
        $user = User::whereOpenid($request->openid)->first();
        $list = CouponCode::where('enabled', 1)
            ->where('not_before', '<=', date('Y-m-d H:i:s'))
            ->where('not_after', '>=', date('Y-m-d H:i:s'))
            ->where('total', '>', 0)
            ->where('is_active', 1)
            ->get(['id', 'name']);

        $list = $list->map(function ($v) use ($user){
            return [
              'id' => $v->id,
              'name' => $v->name,
              'is_lq' => UserCoupon::whereUId($user->id)->where('coupon_id',$v->id)->first()? 0 : 1
            ];
        });

        return $this->response->array([
            'count' => $list->sum('is_lq'),
            'list' => $list,
            'yqhimg' => env('APP_URL') . '/uploads/' . Img::where('type', 8)->first()->image
        ]);
    }

    public function lqyhq(Request $request)
    {
        $list = CouponCode::where('enabled', 1)
            ->where('not_before', '<=', date('Y-m-d H:i:s'))
            ->where('not_after', '>=', date('Y-m-d H:i:s'))
            ->where('total', '>', 0)
            ->where('is_active', 1)
            ->get();

        foreach ($list as $v) {
            $rew = UserCoupon::firstOrCreate([
                'u_id' => $request['userinfo']['id'],
                'coupon_id' => $v->id
            ], [
                'u_id' => $request['userinfo']['id'],
                'coupon_id' => $v->id
            ]);
            if (!isset($rew->is_used)) {
                $v->changeUsed();
            }
        }

        return 200;
    }

    public function lqYhqZf(Request $request)
    {
        //查找是否还存在优惠券
        $data = UserCoupon::where('u_id',$request->p_id)->where('coupon_id',$request->coupon_id)->first();
        if ($request->p_id == $request['userinfo']['id']) {
            return $this->response->array([
                'code' => 411,
                'msg' => '无法领取自己的优惠券'
            ]);
        }
        if (UserCoupon::where('u_id',$request['userinfo']['id'])->where('coupon_id',$request->coupon_id)->first()) {
            return $this->response->array([
                'code' => 411,
                'msg' => '亲~您已经领取过哦'
            ]);
        }
        if ($data) {
            UserCoupon::create([
                'u_id' => $request['userinfo']['id'],
                'coupon_id' => $request->coupon_id,
                'is_used' => false,
                'p_id' => $request->p_id
            ]);
            $data->delete();
            return $this->response->array([
                'code' => 200,
                'msg' => '领取成功'
            ]);
        } else {
            return $this->response->array([
                'code' => 400,
                'msg' => '亲~该优惠券已被全部领取'
            ]);
        }
    }


    /**
     * 获取我的优惠券
     * @return
     */
    public function getMyYqhList(Request $request)
    {
        $goods = Product::find($request->goods_id);
        //可是用优惠券
        $goods_conpons = $goods->coupons;
        $fuser = User::where('openid',$request->u_id)->first();
        if ($fuser) {
            $list = UserCoupon::whereUId($request['userinfo']['id'])
                ->whereIn('p_id',[0,$fuser['id']])
                ->whereHas('yhq',function($query){
                    $query  ->where('not_before', '<=', date('Y-m-d H:i:s'))
                        ->where('not_after', '>=', date('Y-m-d H:i:s'));
                })
                ->where('is_used',0)->get(['id','coupon_id']);
        } else {
            $list = UserCoupon::whereUId($request['userinfo']['id'])
                ->where('p_id',0)
                ->whereHas('yhq',function($query){
                    $query  ->where('not_before', '<=', date('Y-m-d H:i:s'))
                        ->where('not_after', '>=', date('Y-m-d H:i:s'));
                })->where('is_used',0)->get(['id','coupon_id']);
        }

        if (!$list->isEmpty()) {
            //用户可用的优惠券
            $available_coupons = [];
            foreach ($list as $v) {
                if (in_array($v->coupon_id,$goods_conpons)) {
                    $available_coupons[] = $v;
                }
            }
            $arr = [];
            $yqh = '';

            foreach ($available_coupons as $k=>$v) {
                $arr[$k]['id'] = $v->id;
                $arr[$k]['name'] = CouponCode::find($v->coupon_id)->name;
                $arr[$k]['type'] = CouponCode::find($v->coupon_id)->type;
                $arr[$k]['value'] = CouponCode::find($v->coupon_id)->value;
                $arr[$k]['min_amount'] = CouponCode::find($v->coupon_id)->min_amount;
                $arr[$k]['is_check'] = false;
                if ($arr[$k]['min_amount'] <= $goods->price) {
                    $arr[$k]['can_use'] = true;
                } else {
                    $arr[$k]['can_use'] = false;
                }
            }
            $discount = 0;
            foreach ($arr as $k => $v){
                if ($goods->price >= $arr[$k]['min_amount']) {
                    $coupon = UserCoupon::find($arr[$k]['id'])->yhq;
                    $discount = $coupon->getAdPrice($goods->price);
                    $arr[$k]['is_check'] = true;
                    $yqh = $arr[$k];
                    break;
                }
            }
        } else {
            $arr = [];
            $discount = 0;
            $yqh = '';
        }


        return $this->response->array([
            'list' => $arr,
            'discount' => $discount,
            'yhq' => $yqh
        ]);
    }

    /**
     * 获取我的优惠券
     * @return
     */
    public function getMyYqhListByopenid(Request $request)
    {

        $list = UserCoupon::whereUId($request['userinfo']['id'])
            ->whereHas('yhq',function($query){
                $query  ->where('not_before', '<=', date('Y-m-d H:i:s'))
                    ->where('not_after', '>=', date('Y-m-d H:i:s'));
            })
            ->where('is_used',0)->get();

        $list = $list->map(function ($v){
           return [
             'id' => $v->id,
             'coupon_id' => $v->coupon_id,
             'u_id' => $v->u_id,
             'name' => CouponCode::find($v->coupon_id)->name,
             'value' => CouponCode::find($v->coupon_id)->value,
             'desc' =>  CouponCode::find($v->coupon_id)->desc,
             'description' =>  CouponCode::find($v->coupon_id)->description,
             'time' =>  substr(CouponCode::find($v->coupon_id)->not_before,0,10) .'~'. substr(CouponCode::find($v->coupon_id)->not_after,0,10),
             'num' => 1
           ];
        });
        $arr = [];
        $yhqids = [];
        foreach ($list as $k=>$v) {
            if (in_array($v['coupon_id'],$yhqids)) {
                $index = array_search($v['coupon_id'],$yhqids);
                $arr[$index]['num'] = $arr[$index]['num'] + 1;
            } else {
                $yhqids[] = $v['coupon_id'];
                $arr[] = $v;
            }
        }

        return $this->response->array([
            'list' => $arr,
        ]);
    }


    public function getZfYhq(Request $request)
    {
        $list = UserCoupon::where('u_id',$request->u_id)->where('coupon_id',$request->id)->get();
        $data = '';

        if (count($list)>0) {
            $data = [
                'id' => $list[0]->id,
                'coupon_id' => $list[0]->coupon_id,
                'u_id' => $list[0]->u_id,
                'name' => CouponCode::find($list[0]->coupon_id)->name,
                'value' => CouponCode::find($list[0]->coupon_id)->value,
                'desc' =>  CouponCode::find($list[0]->coupon_id)->desc,
                'description' =>  CouponCode::find($list[0]->coupon_id)->description,
                'time' =>  substr(CouponCode::find($list[0]->coupon_id)->not_before,0,10) .'~'. substr(CouponCode::find($list[0]->coupon_id)->not_after,0,10),
                'num' => count($list),
                'goodslist' => Product::where('coupons','like','%'.$list[0]->coupon_id.'%')->get(['id','title','pics','price'])->map(function ($v) use ($list) {
                    return [
                        'id' => $v->id,
                        'pic' => env('APP_URL').'/uploads/'.$v->pics[0],
                        'title'=> $v->title,
                        'price'=> $v->price,
                        'qhj'=> round(CouponCode::find($list[0]->coupon_id)->getAdjustedPrice($v->price),2),
                    ];
                })
            ];

        }
        return $this->response->array([
            'data' => $data
        ]);
    }


    public function getbuyid(Request $request)
    {
        $data = GoodsDb::find($request->id);
        $data->avatar = env('APP_URL').'/uploads/'.$data->avatar;
        $data->nick =  mb_substr($data->nick,0,1).'***'.mb_substr($data->nick,mb_strlen($data->nick,"utf-8")-1,mb_strlen($data->nick,"utf-8"));
        return $this->response->array([
            'data' => $data
        ]);
    }
}
