<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/ps', 'DfhorderController@ps');
    $router->get('/getCateList',function () {
        $list = \App\GoodsCate::orderBy('sort','asc')->get(['id','name as text']);
        return $list;
    });
    $router->get('/getGysList',function () {
        $list = \App\Gys::where('status',1)->get(['id','name as text']);
        return $list;
    });
    $router->get('/getxsuser',function () {
        $list = \App\User::where('user_type',2)->get(['id','nickname as text']);
        return $list;
    });
    $router->get('/api/users',function (\Illuminate\Http\Request $request) {
        $q = $request->get('q');
        return \App\User::where('openid', 'like', "%$q%")->paginate(null, ['id', 'nickname as text']);
    });
    $router->get('/getLeader',function () {
        $list = \App\User::where('is_leader',1)->get(['id','nickname as text']);
        return $list;
    });
    $router->resource('/goodscate', GoodsCateController::class);
    $router->resource('/product',ProductController::class);
    $router->resource('/group', GroupController::class);
    $router->resource('/groupshop', GroupShopController::class);
    $router->resource('/imgs', ImgsController::class);
    $router->resource('/users', UserController::class);
    $router->resource('/goodsdb', GoodsDbController::class);
    $router->resource('/gg', GgController::class);
    $router->resource('/order', OrderController::class);
    $router->resource('/leaderzc', LeaderZcController::class);
    $router->resource('/thdzc', ThdZcController::class);
    $router->resource('/dfhorder', DfhorderController::class);
    $router->resource('/xsuser', XsUserController::class);
    $router->resource('/txmsg', TxMsgController::class);
    $router->resource('/news', NewsController::class);
    $router->resource('/gys_type', GysTypeController::class);
    $router->resource('/gys', GysController::class);
    $router->resource('/commodity', CommodityController::class);
    $router->resource('activegoods', ActiveGoodsController::class);
    $router->resource('coupon_codes', CouponCodesController::class);
    $router->resource('cwxxlr', SpcwxxLrController::class);
});


