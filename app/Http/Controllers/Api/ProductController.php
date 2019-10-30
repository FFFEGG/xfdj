<?php


namespace App\Http\Controllers\Api;


use App\CouponCode;
use App\GoodsDb;
use App\Http\Controllers\Controller;
use App\Img;
use App\Product;
use App\Spec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function getGoodsData(Request $request)
    {
        $data = Product::find($request->id);
        foreach ($data->pics as $v) {
            $arr[] = env('APP_URL') . '/uploads/' . $v;
        }
        $goods_type = 1;//正在开团中

        if ($data->star_time > date('Y-m-d H:i:s', time())) {
            $goods_type = 0;//wei未开始
        }

        if ($data->end_time < date('Y-m-d H:i:s', time())) {
            $goods_type = 2;//结束
        }
        return $this->response->array([
            'data' => [
                'id' => $data->id,
                'end_time' => 3600 * 24,
                'old_price' => $data->old_price,
                'price' => $data->price,
                'pics' => $arr,
                'ps_time' => substr($data->ps_time,5,2).'月'.substr($data->ps_time,8,2).'日',
                'sales_num' => $data->status==2?0:$data->sales_num,
                'stock' => $data->stock,
                'title' => $data->title,
                'xg_num' => $data->xg_num,
                'is_xg' => $data->is_xg,
                'content' => $data->content,
                'content_bg' => env('APP_URL') . '/uploads/' . Img::whereType(2)->first()['image'],
                'fx_img' => $data->fx_img?env('APP_URL') . '/uploads/' . $data->fx_img:$arr[0],
                'gyjg' => config('gyjg'),
                'gybz' => config('gybz'),
                'gyqs' => config('gyqs'),
                'gyzl' => config('gyzl'),
                'specs' => count($data->specs)?$data->specs->map(function ($v){
                    return [
                      'id' => $v->id,
                      'name' => $v->name,
                      'price' => $v->price,
                      'thumb' => env('APP_URL') . '/uploads/' .$v->thumb,
                    ];
                }): '',
                'goods_type' => $goods_type,
                'min_qs' => $data->min_qs,
                'coupons'=> $data->coupons[0]?$data->coupons:0
            ]
        ]);
    }


    public function getGoodsDataBysh(Request $request)
    {
        $data = Product::whereIsSj(1)->find($request->id);
        foreach ($data->pics as $v) {
            $arr[] = env('APP_URL') . '/uploads/' . $v;
        }
        $goods_type = 1;//正在开团中

        if ($data->star_time > date('Y-m-d H:i:s', time())) {
            $goods_type = 0;//wei未开始
        }

        if ($data->end_time < date('Y-m-d H:i:s', time())) {
            $goods_type = 2;//结束
        }
        return $this->response->array([
            'data' => [
                'id' => $data->id,
                'end_time' => strtotime($data->end_time) - time(),
                'old_price' => $data->old_price,
                'price' => $data->price,
                'pics' => $arr,
                'ps_time' => substr($data->ps_time,5,2).'月'.substr($data->ps_time,8,2).'日',
                'sales_num' => $data->sales_num,
                'stock' => $data->stock,
                'title' => $data->title,
                'xg_num' => $data->xg_num,
                'content' => $data->content,
                'content_bg' => env('APP_URL') . '/uploads/' . Img::whereType(2)->first()['image'],
                'gyjg' => config('gyjg'),
                'gybz' => config('gybz'),
                'gyqs' => config('gyqs'),
                'gyzl' => config('gyzl'),
                'goods_type' => $goods_type,
                'kz' => $data->shsy
            ]
        ]);
    }

    public function getGoodsDataById(Request $request)
    {
        $weekarray = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];

        $data = Product::whereIsSj(1)->find($request->id);
        return $this->response->array([
            'data' => [
                'id' => $data->id,
                'price' => $request->spec_id?Spec::where('goods_id',$request->id)->find($request->spec_id)['price']:$data->price,
                'xg_num' => $data->xg_num,
                'is_xg' => $data->is_xg,
                'thumb' => env('APP_URL') . '/uploads/' . $data->pics[0],
                'title' => $data->title,
                'min_qs' => $data->min_qs,
                'time' => substr($data->ps_time,5,2).'月'.substr($data->ps_time,8,2).'日',
                'week' => $weekarray[date("w", strtotime("+1 day"))],
                'stock' => $data->stock,
                'spec' => $request->spec_id?Spec::find($request->spec_id)['name']:'',
                'yhqlist' => CouponCode::whereIn('id', $data->coupons)->get(['id','min_amount','type','value','name'])
            ]
        ]);
    }

    public function getGoodsList(Request $request)
    {
        $weekarray = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];



        foreach ($request->list as $k=>$v) {
            $data[] = Product::whereIsSj(1)->find($v);
        }



        $zprice = 0;
        foreach ($data as $k=>$v) {
            if ($request->spec_ids[$k]) {
                $zprice = ($zprice * 100 + Spec::find($request->spec_ids[$k])['price'] * $request->num[$k] * 100) / 100;
            } else {
                $zprice = ($zprice * 100 + $v->price * $request->num[$k] * 100) / 100;
            }

        }

        $data = collect($data)->map(function ($v,$k) use ($weekarray,$request) {
            return [
                'id' => $v->id,
                'price' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['price']:$v->price,
                'thumb' => $request->spec_ids[$k]?env('APP_URL') . '/uploads/' .Spec::find($request->spec_ids[$k])['thumb']:env('APP_URL') . '/uploads/' . $v->pics[0],
                'title' => $v->title,
                'is_xg' => $v->is_xg,
                'spec_id' => $request->spec_ids[$k],
                'spec' => $request->spec_ids[$k]?Spec::find($request->spec_ids[$k])['name']:'',
                'ps_time' => substr($v->ps_time,5,2).'月'.substr($v->ps_time,8,2).'日',
                'num' => $request->num[$k]
            ];
        });

        $data = $data->groupBy('ps_time');


        return $this->response->array([
            'data' => $data,
            'zprice' => $zprice,
            'week' => $weekarray[date("w", strtotime("+1 day"))]
        ]);
    }

    public function arrayToObject($e)
    {

        if (gettype($e) != 'array') return;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)$this->arrayToObject($v);
        }
        return (object)$e;
    }

    public function getOpenidTime()
    {
        return $this->response->array([
            'data' => intval(config('openid_time'))
        ]);
    }

    public function bothBuy(Request $request)
    {
        $list = Product::whereIsSj(1)->where('id', '!=', $request->id)->orderBy('real_sales', 'desc')->limit(2)->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'image' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => $v->price
            ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function getPersons()
    {
        $list = GoodsDb::inRandomOrder()->limit(11)->get();
        $list = $list->map(function ($v) {
            return [
                'nick' => $v->nick,
                'avatar' => env('APP_URL') . '/uploads/' . $v->avatar,
            ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }

    public function getTimeOverGoods()
    {
        $rew = Product::where('end_time', '<', date('Y-m-d H:i:s', time()))
            ->get(['id']);

        return $this->response->array([
            'data' => $rew
        ]);
    }

    public function getNotes(Request $request)
    {
        $product = Product::find($request->id);
        $productsales_num = min(150,$product->sales_num);
        if (Cache::get($product->sales_num.$product->id)) {
            $list = Cache::get($product->sales_num.$product->id);
        } else {
            $list = GoodsDb::inRandomOrder()->limit($productsales_num)->get();
            $list = $list->map(function ($v) {
                return [
                    'nick' => mb_substr($v->nick,0,1).'***'.mb_substr($v->nick,mb_strlen($v->nick,"utf-8")-1,mb_strlen($v->nick,"utf-8")),
                    'avatar' => env('APP_URL') . '/uploads/' . $v->avatar,
                    'num' => rand(1,2)
                ];
            });
            Cache::put($product->sales_num.$product->id, $list, 60 * 24);
        }
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function getgoodslistbysh(Request $request)
    {
        $data = Product::whereIsShop(1)->paginate(5);
        $data = $data->map(function ($v){
            return [
                'id' => $v->id,
                'price' => $v->price,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'title' => $v->title,
                'old_price' => $v->old_price,
                'stock' => $v->stock,
                'sales_num' => $v->sales_num,
            ];
        });
        return $this->response->array([
            'data' => $data
        ]);
    }
}
