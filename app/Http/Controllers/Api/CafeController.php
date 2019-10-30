<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Restaurant;
use Illuminate\Http\Request;


class CafeController extends Controller
{
    public function getCafeCont(Request $request)
    {
        $cafe = Restaurant::with(['catelist' => function ($query) {
            $query->where('is_show', 1)->orderBy('sort', 'asc');
        }, 'catelist.cafegoods' => function ($query) {
            $query->where('is_sj', 1);
        }, 'catelist.cafegoods.specs'])->find($request->id);
        $cafe->thumb = env('APP_URL') . '/uploads/' . $cafe->thumb;
        foreach ($cafe->catelist as $k => $v) {
            $cafe->catelist[$k]['num'] = 0;
            foreach ($v->cafegoods as $ki => $vi) {
                $v->cafegoods[$ki]['thumb'] = env('APP_URL') . '/uploads/' . $vi->thumb;
            }
        }

        return $this->response->array([
            'list' => $cafe
        ]);
    }

}
