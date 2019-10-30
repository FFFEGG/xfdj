<?php


namespace App\Http\Controllers\Api;


use App\CouponCode;
use App\Http\Controllers\Controller;
use App\Product;
use App\Spec;
use App\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ShoppingController extends Controller
{
    /*
    * 添加
    * */
    public function addgoods(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        $key = $request->goods_id . $request->spec;
        $goods = Product::find($request->goods_id);

        if ($goods->min_qs > 1) {

            $value = [
                'goods_id' => $request->goods_id,
                'num' => $goods->min_qs,
                'spec' => $request->spec,
                'is_check' => true,
                'coupon_id' => 0
            ];
        } else {

            $value = [
                'goods_id' => $request->goods_id,
                'num' => 1,
                'spec' => $request->spec,
                'is_check' => true,
                'coupon_id' => 0
            ];
        }


        if ($tmp_Users_tab) {
            //检查该商品ID是否存在;
            $Shopp = Redis::hexists($table_name, $key);
            if ($Shopp) {
                //存在;
                //取出该商品数;
                $goods = json_decode(Redis::hGet($table_name, $key));
                $num = 0;
                $product = Product::find($goods->goods_id);
                if ($product->is_xg) {
                    if ($product->xg_num > $goods->num) {
                        $num = 1;
                        $goods->num++;
                    }
                } else {
                    $num = 1;
                    $goods->num++;
                }
                //修改完之后的保存;
                Redis::hset($table_name, $key, json_encode($goods));
                return $num;
            } else {
                $rew = Redis::hset($table_name, $key, json_encode($value));
                return 1;
            }
        } else {
            //用户表不存在;
            $rew = Redis::hset($table_name, $key, json_encode($value));
            return 1;
        }
    }

    /*
    * 添加
    * */
    public function check_goods(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;

        $Shopp = Redis::hexists($table_name, $key);
        if ($Shopp) {
            //取出该商品数;
            $goods = json_decode(Redis::hGet($table_name, $key));
            $goods->is_check = $request->is_check;
            //修改完之后的保存;
            Redis::hset($table_name, $key, json_encode($goods));
            return Redis::hGet($table_name, $key);
        } else {
            return 400;
        }

    }

    /**
     * 查找我的购物车
     * @param Request $request
     * @return array
     */
    public function getMyCart(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        if ($tmp_Users_tab) {
            $list = Redis::hvals($table_name);
            foreach ($list as $k => $v) {
                $data = json_decode($v);
                $goods = Product::find($data->goods_id);
                $time = date('Y-m-d H:i:s', time());
                if ($goods->star_time <= $time && $goods->end_time >= $time) {
                    $list[$k] = json_decode($v);
                } else {
                    $key = $data->goods_id . $data->spec;
                    $Shopp = Redis::hexists($table_name, $key);
                    if ($Shopp) {
                        Redis::hdel($table_name, $key);
                    }
                }
            }
            $list = collect($list);

            $list = $list->map(function ($v) use ($table_name) {
                $goods = Product::find($v->goods_id);
                return [
                    'id' => $goods->id,
                    'thumb' => env('APP_URL') . '/uploads/' . $goods->pics[0],
                    'title' => $goods->title,
                    'num' => $v->num,
                    'price' => $goods->price,
                    'zprice' => $goods->price * $v->num,
                    'spec' => Spec::find($v->spec)?Spec::find($v->spec)->id:null,
                    'is_check' => $v->is_check,
                    'coupons' => $goods->coupons,
                    'coupon_id' => $v->coupon_id,
                    'xg_num' => $goods->is_xg? $goods->xg_num:$goods->stock
                ];
            });
            return $this->response->array([
                'list' => $list,
                'num' => $list->sum('num')
            ]);
        } else {
            return $this->response->array([
                'list' => [],
                'num' => 0
            ]);
        }
    }
    /**
     * 查找我的购物车
     * @param Request $request
     * @return array
     */
    public function getmycheckcart(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        if ($tmp_Users_tab) {
            $lists = Redis::hvals($table_name);
            foreach ($lists as $k => $v) {
                $data = json_decode($v);
                if ($data->is_check) {
                    $list[$k] = json_decode($v);
                }
            }
            $list = collect($list);
            $list = $list->map(function ($v) use ($table_name) {
                $goods = Product::find($v->goods_id);
                if ($v->spec) {
                    $price = Spec::find($v->spec)->price;
                } else {
                    $price = $goods->price;
                }
                if ($v->is_check) {
                    return [
                        'id' => $goods->id,
                        'thumb' => env('APP_URL') . '/uploads/' . $goods->pics[0],
                        'title' => $goods->title,
                        'num' => $v->num,
                        'price' => $price,
                        'zprice' => max(0.01,round($price * $v->num - ($v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0),2)),
                        'spec' => $v->spec?Spec::find($v->spec)->name:'',
                        'is_check' => $v->is_check,
                        'coupons' => $goods->coupons,
                        'coupon_id' => $v->coupon_id,
                        'yhq'=>$v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->name:'',
                        'week' => substr($goods->ps_time,0,10),
                        'xg_num' => $goods->is_xg? $goods->xg_num:$goods->stock,
                        'is_xg' => $goods->is_xg,
                        'zk' => $v->coupon_id?UserCoupon::find($v->coupon_id)->yhq->getAdPrice($price * $v->num):0
                    ];
                }
            });
            return $this->response->array([
                'list' => $list,
                'zprice' => $list->sum('zprice'),
                'zk' => round($list->sum('zk'),2)
            ]);

        } else {
            return $this->response->array([
                'list' => []
            ]);
        }

    }


    /**
     * 增加数量
     * @return int
     */
    public function increasegoods(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;
        $Shopp = Redis::hexists($table_name, $key);
        if (!$Shopp) {
            return 200;
        }
        //取出该商品数;
        $goods = json_decode(Redis::hGet($table_name, $key));
        $goods->num++;
        //修改完之后的保存;
        Redis::hset($table_name, $key, json_encode($goods));
        return Redis::hGet($table_name, $key);

    }


    /**
     * 减少数量
     * @return int
     */
    public function delgoodsnum(Request $request)
    {
        $product = Product::find($request->goods_id);

        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;
        $Shopp = Redis::hexists($table_name, $key);
        if (!$Shopp) {
            return 200;
        }
        //取出该商品数;
        $goods = json_decode(Redis::hGet($table_name, $key));
        $goods->num--;
        if ($goods->num == 0 ||  $goods->num < $product->min_qs) {
           return Redis::hdel($table_name, $key);
        }
        //修改完之后的保存;
        Redis::hset($table_name, $key, json_encode($goods));
        return Redis::hGet($table_name, $key);

    }


    /**
     * 删除
     * @return int
     */
    public function delgoods(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;
        $Shopp = Redis::hexists($table_name, $key);
        if (!$Shopp) {
            return 200;
        }
        return Redis::hdel($table_name, $key);
    }

    /**
     * 增加优惠券
     * @return int
     */
    public function addyhq(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;
        $Shopp = Redis::hexists($table_name, $key);
        if (!$Shopp) {
            return 200;
        }
        $goods = json_decode(Redis::hGet($table_name, $key));
        $goods->coupon_id = $request->coupon_id;
        Redis::hset($table_name, $key, json_encode($goods));
        return Redis::hGet($table_name, $key);
    }

    /**
     * 去掉优惠券
     * @return int
     */
    public function delyhq(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];

        $key = $request->goods_id . $request->spec;
        $Shopp = Redis::hexists($table_name, $key);
        if (!$Shopp) {
            return 200;
        }
        $goods = json_decode(Redis::hGet($table_name, $key));
        $goods->coupon_id = 0;
        Redis::hset($table_name, $key, json_encode($goods));
        return Redis::hGet($table_name, $key);
    }

    public function canclecoupons(Request $request)
    {
        $table_name = 'ShoppingCar:' . $request['userinfo']['id'];
        $tmp_Users_tab = Redis::exists($table_name);
        if ($tmp_Users_tab) {
            $list = Redis::hvals($table_name);

            foreach ($list as $k => $v) {
                $data = json_decode($v);
                $key = $data->goods_id . $data->spec;
                $data->coupon_id = 0;
                Redis::hset($table_name, $key, json_encode($data));
            }
            return 200;
        } else {
            return 400;
        }
    }

}
