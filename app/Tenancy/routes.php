<?php

Route::get('/', 'HomeController@index');
Route::resource('/gys', GysController::class);
Route::post('/gys/{id}/edit', 'GysController@edit');
Route::resource('/goodslist', GoodsController::class);
Route::resource('/products', ProductController::class);
Route::resource('/orders', OrderController::class);
Route::resource('/tgendorder', TgEndOrderController::class);
Route::resource('/goodscate', GoodsCateController::class);
Route::resource('/news', NewsController::class);
Route::resource('/imgs', ImgsController::class);
Route::resource('/gg', GgController::class);
Route::resource('/goodsdb', GoodsDbController::class);
Route::resource('/shorder', ShOrderController::class);
Route::resource('/group', GroupController::class);
Route::resource('/avtives', AvtiveController::class);
Route::resource('/avtivegoods', ActiveGoodsController::class);
Route::resource('/zcavtivegoods', ZcActiveGoodsController::class);
Route::resource('drawrecords', DrawRecordsController::class);
Route::resource('coupon_codes', CouponCodesController::class);
Route::resource('shsmorder', ShSmOrderController::class);
Route::resource('qun', QunCode::class);
Route::resource('usercoupon', UserCouponController::class);
Route::resource('region', RegionController::class);
Route::resource('product-types', ProductTypeController::class);
Route::resource('cgies', CgyController::class);

Route::get('/getCateList',function () {
    $list = \App\GoodsCate::orderBy('sort','asc')->get(['id','name as text']);
    return $list;
});

Route::get('/tz',function (\Illuminate\Http\Request $request) {
    $order = \App\TgEndOrder::find($request->id);
    if (!$order) {
        $error = new \Illuminate\Support\MessageBag([
            'title'   => '操作失败',
            'message' => '不存在订单',
        ]);
        return back()->with(compact('error'));
    }

    if ($order->end_time > date('Y-m-d H:i:s',time())) {
        $error = new \Illuminate\Support\MessageBag([
            'title'   => '操作失败',
            'message' => '该产品拼团时间还没结束',
        ]);
        return back()->with(compact('error'));
    }

    $order->status = 1;
    $order->save();
    $success = new \Illuminate\Support\MessageBag([
        'title'   => '操作成功',
        'message' => '已通知供应商',
    ]);
    return back()->with(compact('success'));

});

Route::any('/shordersh',function (\Illuminate\Http\Request $request,\Encore\Admin\Layout\Content $content){

    if ($request->isMethod('post')) {
        $order = \App\ShOrder::find($request->id);
        $order->status = 1;
        $order->save();
        $success = new \Illuminate\Support\MessageBag([
            'title'   => '操作成功',
            'message' => '',
        ]);
        return redirect('/tenancy/shorder')->with(compact('success'));

    }

    $order = \App\ShOrder::with(['msg','msg.goods'])->whereId($request->id)->first();
    return $content
        ->header('商户订单审核')
        ->body(view('merchant.sh',compact('order')));
});


Route::get('/getGysList',function () {
    $list = \App\Gys::where('status',1)->get(['id','name as text']);
    return $list;
});
Route::get('/api/users',function (\Illuminate\Http\Request $request) {
    $q = $request->get('q');
    return \App\User::where('openid', 'like', "%$q%")->paginate(null, ['id', 'nickname as text']);
});

Route::get('/getLeader',function () {
    $list = \App\User::where('is_leader',1)->get(['id','nickname as text']);
    return $list;
});

Route::get('/getgystype', function (){
    $list = \App\GysType::get(['id','name as text']);
    return $list;
});
