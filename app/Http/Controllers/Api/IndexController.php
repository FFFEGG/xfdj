<?php


namespace App\Http\Controllers\Api;


use App\CafeImg;
use App\Gg;
use App\Http\Controllers\Controller;
use App\Img;
use App\News;
use App\Product;
use Illuminate\Http\Request;


class IndexController extends Controller
{
    public function getTjList()
    {
        $list = Product::whereIsTj(1)
            ->whereIsSj(1)
            ->where('cate_id','!=',6)
            ->where('cate_id','!=',7)
            ->where('cate_id','!=',8)
//            ->where('star_time', '<=', date('Y-m-d H:i:s', time()))
            ->where('end_time', '>=', date('Y-m-d H:i:s', time()))
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => number_format($v->price, 2),
                'old_price' => number_format($v->old_price, 2),
                'tag' => $v->star_time <= date('Y-m-d H:i:s', time()) ? '秒杀中' : '即将开团'
            ];
        });
        return $this->response->array([
            'data' => $list,
            'imgs' => [
                'img1' =>  env('APP_URL') . '/uploads/' . Img::where('type',3)->first()['image'],
                'img2' =>  env('APP_URL') . '/uploads/' . Img::where('type',4)->first()['image'],
                'img3' =>  env('APP_URL') . '/uploads/' . Img::where('type',5)->first()['image'],
                'img4' =>  env('APP_URL') . '/uploads/' . Img::where('type',7)->first()['image'],
            ]
        ]);
    }
    public function gettomorrow()
    {
        $list = Product::whereIsYg(1)
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => number_format($v->price, 2),
                'old_price' => number_format($v->old_price, 2),
            ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }


    public function gethotlist(Request $request)
    {
        $list = Product::whereIsSj(1)
//            ->where('cate_id','!=',6)
//            ->where('cate_id','!=',7)
//            ->where('cate_id','!=',8)
            ->where('stock','>',0)
////        ->where('end_time', '>=', date('Y-m-d H:i:s', time()))
            ->orderBy('status', 'asc')
            ->orderBy('sort', 'asc')
//            ->orderBy('end_time', 'desc')
            ->orderBy('created_at', 'desc')
//          ->orderBy('created_at', 'desc')
            ->offset(($request->page - 1) * 6)
            ->limit(6)
            ->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => number_format($v->price, 2),
                'old_price' => number_format($v->old_price, 2),
                'stock' => $v->is_xg?($v->stock > $v->xg_num ?$v->xg_num : $v->stock): $v->stock,
                'stocks' => $v->stock,
                'sales_num' => $v->status==2?0:$v->sales_num,
                'is_check' => false,
                'is_xg' => $v->is_xg,
                'status' =>$this->iskt($v->star_time,$v->end_time),
                'status_name' =>$this->status_name($v->status),
                'specs' => count($v->specs)?$v->specs->map(function ($vi){
                    return [
                        'id' => $vi->id,
                        'name' => $vi->name,
                        'price' => $vi->price,
                        'thumb' => env('APP_URL') . '/uploads/' .$vi->thumb,
                    ];
                }): '',
                'num' => 0,
                'spec_id' => 0,
                'min_qs' => $v->min_qs,
                'coupons'=> $v->coupons[0]?$v->coupons:0,
                'star_time' => substr($v->star_time,0,10),
                'end_time' => substr($v->end_time,0,10),
            ];
        });
        return $this->response->array([
            'data' => $list,
            'pages' => ceil(Product::whereIsSj(1)->count() / 6)
        ]);
    }

    public function iskt($startime,$endtime)
    {
        if ($startime <= date('Y-m-d H:i:s',time()) && $endtime >= date('Y-m-d H:i:s',time())) {
            return true;
        } else {
            return false;
        }
    }

    public function status_name($status)
    {
        switch ($status){
            case 1:
                return '开团中';
                break;
            case 2:
                return '即将开团';
                break;
            case 3:
                return '已结束';
                break;
        }
    }

    public function getLb()
    {
        $list = Img::whereType(1)->orderBy('created_at','desc')->get();
        $list = $list->map(function ($v) {
            return [
                'image' => env('APP_URL') . '/uploads/' . $v->image,
                'url' => $v->url,
                'url_type' => $v->url_type,
            ];
        });
        $gg = Gg::get();
        $ms = CafeImg::get();
        $ms = $ms->map(function ($v) {
            return [
                'img' => env('APP_URL') . '/uploads/' . $v->img
            ];
        });
        return $this->response->array([
            'lb' => $list,
            'news' => $gg,
            'ms' => $ms
        ]);
    }


    public function getNews()
    {
        return $this->response->array([
            'data'=> [
                'gwlc' => env('APP_URL').'/news/'.News::whereType(1)->first()['id'],
                'cjwt' => env('APP_URL').'/news/'.News::whereType(2)->first()['id'],
                'abus' => env('APP_URL').'/news/'.News::whereType(3)->first()['id'],
                'qrdh' => env('APP_URL').'/news/7',
            ]
        ]);
    }

    public function search(Request $request)
    {
        $list = Product::where('title','like','%'.$request->p.'%')
            // ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        $list = $list->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => number_format($v->price, 2),
                'old_price' => number_format($v->old_price, 2),
            ];
        });
        return $this->response->array([
            'data' => $list
        ]);
    }

    public function searchbyindex(Request $request)
    {
        if ($request->type == 1) {
            $ids = 6;
            $img = env('APP_URL').'/uploads/'.Img::where('id',20)->first()->image;
        }

        if ($request->type == 2) {
            $ids = 7;
            $img = env('APP_URL').'/uploads/'.Img::where('id',21)->first()->image;
        }


        if ($request->type == 3) {
            $ids = 8;
            $img = env('APP_URL').'/uploads/'.Img::where('id',19)->first()->image;
        }

        $list = Product::where('cate_id',$ids)
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        $list = $list->map(function ($v)  {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => env('APP_URL') . '/uploads/' . $v->pics[0],
                'price' => number_format($v->price, 2),
                'old_price' => number_format($v->old_price, 2),
                'is_show' => false,
            ];
        });
        return $this->response->array([
            'data' => $list,
            'img' => $img
        ]);
    }
}
