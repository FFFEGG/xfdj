<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
],function($api) {
    $api->post('login', 'UserController@login');
    $api->get('getAccessToken', 'UserController@getAccessToken')->middleware('openid');
    $api->get('getNews', 'IndexController@getNews');
    $api->post('getSessionKey', 'UserController@getSessionKey');
    $api->post('updateUserInfo', 'UserController@updateUserInfo');
    $api->post('checkUserInfo', 'UserController@checkUserInfo');
    $api->post('createOrder', 'UserController@createOrder')->middleware('openid');
    $api->post('createOrderv2', 'UserController@createOrderv2')->middleware('openid');
    $api->post('createOrderAddYhqv2', 'UserController@createOrderAddYhqv2')->middleware('openid');
    $api->post('createOrderAddYhq', 'UserController@createOrderAddYhq')->middleware('openid');
    $api->post('createOrderByList', 'UserController@createOrderByList')->middleware('openid');
    $api->post('createOrderByListAddYhq', 'UserController@createOrderByListAddYhq')->middleware('openid');
    $api->post('createOrderByListAddYhqv2', 'UserController@createOrderByListAddYhqv2')->middleware('openid');
    $api->post('getMyTeamByOpenid', 'UserController@getMyTeamByOpenid')->middleware('openid');
    $api->post('addformid', 'UserController@addformid')->middleware('openid');
    $api->post('th', 'UserController@th')->middleware('openid');
    $api->post('thById', 'UserController@thById')->middleware('openid');
    $api->post('thByUserId', 'UserController@thByUserId')->middleware('openid');
    $api->post('thByUserIdAddThd', 'UserController@thByUserIdAddThd')->middleware('openid');
    $api->post('tx', 'UserController@tx')->middleware('openid');
    $api->post('getMySy', 'UserController@getMySy')->middleware('openid');
    $api->post('getMyDjs', 'UserController@getMyDjs')->middleware('openid');
    $api->post('calluser', 'UserController@calluser')->middleware('openid');
    $api->post('getOrderList', 'UserController@getOrderList')->middleware('openid');
    $api->post('getOrderData', 'UserController@getOrderData')->middleware('openid');
    $api->post('payNowWfk', 'UserController@payNowWfk')->middleware('openid');
    $api->post('LeaderZcZf', 'UserController@LeaderZcZf')->middleware('openid');
    $api->post('getmytxmsg', 'UserController@getmytxmsg')->middleware('openid');
    $api->post('LeaderZcZfcode', 'UserController@LeaderZcZfcode')->middleware('openid');
    $api->post('getthdlist', 'UserController@getthdlist')->middleware('openid');
    $api->post('thdzczf', 'UserController@thdzczf')->middleware('openid');
    $api->post('shzczf', 'UserController@shzczf')->middleware('openid');
    $api->post('thdzczfcode', 'UserController@thdzczfcode')->middleware('openid');
    $api->post('findMyLeaderMsg', 'UserController@findMyLeaderMsg')->middleware('openid');
    $api->post('findMyShMsg', 'UserController@findMyShMsg')->middleware('openid');
    $api->post('getUserInfoByid', 'UserController@getUserInfoByid');
    $api->post('scsfz', 'UserController@scsfz')->middleware('openid');
    $api->post('findMyThdMsg', 'UserController@findMyThdMsg')->middleware('openid');
    $api->post('getmythlistbyq', 'UserController@getmythlistbyq')->middleware('openid');
    $api->post('ysshbyid', 'UserController@ysshbyid')->middleware('openid');
    $api->post('ysshbyidaddthd', 'UserController@ysshbyidaddthd')->middleware('openid');
    $api->post('cancelbyid', 'UserController@cancelbyid')->middleware('openid');
    $api->post('pldh', 'UserController@pldh')->middleware('openid');
    $api->get('getLoginBg', 'ImageController@getLoginBg');
    $api->post('getTjList', 'IndexController@getTjList');
    $api->post('gettomorrow', 'IndexController@gettomorrow');
    $api->get('search', 'IndexController@search');
    $api->get('searchbyindex', 'IndexController@searchbyindex');
    $api->post('getLb', 'IndexController@getLb');
    $api->post('gethotlist', 'IndexController@gethotlist');
    $api->post('getGoodsData', 'ProductController@getGoodsData');
    $api->post('getGoodsDataBysh', 'ProductController@getGoodsDataBysh');
    $api->post('getGoodsDataById', 'ProductController@getGoodsDataById');
    $api->post('getGoodsList', 'ProductController@getGoodsList');
    $api->post('bothBuy', 'ProductController@bothBuy');
    $api->post('getPersons', 'ProductController@getPersons');
    $api->post('getGroupList', 'GroupController@getGroupList');
    $api->post('imgUpload', 'UserController@imgUpload');
    $api->get('getTimeOverGoods', 'ProductController@getTimeOverGoods');
    $api->get('getgoodslistbysh', 'ProductController@getgoodslistbysh');
    $api->get('getOpenidTime', 'ProductController@getOpenidTime');
    $api->post('getNotes', 'ProductController@getNotes');
    $api->any('wecaht_notify', 'wechatController@wecaht_notify');
    $api->any('merchant_notify', 'wechatController@merchant_notify');
    $api->post('getCodeByTel', 'MsgCodeController@getCodeByTel');
    $api->post('addcarbysh', 'MerchantCodeController@addcarbysh')->middleware('openid');
    $api->post('getCartList', 'MerchantCodeController@getCartList')->middleware('openid');
    $api->post('delShcart', 'MerchantCodeController@delShcart')->middleware('openid');
    $api->post('getShInfo', 'MerchantCodeController@getShInfo')->middleware('openid');
    $api->post('applyPurchase', 'MerchantCodeController@applyPurchase')->middleware('openid');
    $api->post('getShorderList', 'MerchantCodeController@getShorderList')->middleware('openid');
    $api->post('getMyKc', 'MerchantCodeController@getMyKc')->middleware('openid');
    $api->post('smzf', 'MerchantCodeController@smzf')->middleware('openid');
    $api->post('createShOrder', 'MerchantCodeController@createShOrder')->middleware('openid');
    $api->post('geyspm', 'MerchantCodeController@geyspm');
    $api->post('getCafeCont', 'CafeController@getCafeCont');
    $api->get('getJpList', 'ActiveController@getJpList');
    $api->get('getZcJpList', 'ActiveController@getZcJpList');
    $api->post('LuckDraw', 'ActiveController@LuckDraw');
    $api->post('ZcLuckDraw', 'ActiveController@ZcLuckDraw');
    $api->get('getyhq', 'ActiveController@getyhq');
    $api->post('lqyhq', 'ActiveController@lqyhq')->middleware('openid');
    $api->post('getMyYqhList', 'ActiveController@getMyYqhList')->middleware('openid');
    $api->post('getmyyhq', 'CouponController@getmyyhq')->middleware('openid');
    $api->post('getMyYqhListByopenid', 'ActiveController@getMyYqhListByopenid')->middleware('openid');
    $api->post('getZfYhq', 'ActiveController@getZfYhq');
    $api->post('addgoods', 'ShoppingController@addgoods')->middleware('openid');
    $api->get('getMyCart', 'ShoppingController@getMyCart')->middleware('openid');
    $api->post('increasegoods', 'ShoppingController@increasegoods')->middleware('openid');
    $api->post('delgoodsnum', 'ShoppingController@delgoodsnum')->middleware('openid');
    $api->post('delgoods', 'ShoppingController@delgoods')->middleware('openid');
    $api->post('check_goods', 'ShoppingController@check_goods')->middleware('openid');
    $api->post('addyhq', 'ShoppingController@addyhq')->middleware('openid');
    $api->post('canclecoupons', 'ShoppingController@canclecoupons')->middleware('openid');
    $api->post('delyhq', 'ShoppingController@delyhq')->middleware('openid');
    $api->post('getmycheckcart', 'ShoppingController@getmycheckcart')->middleware('openid');
    $api->post('lqYhqZf', 'ActiveController@lqYhqZf')->middleware('openid');
    $api->get('getbuyid', 'ActiveController@getbuyid');
});

$api->version('v2', function($api) {
    $api->get('version', function() {
        return response('this is version v2');
    });
});
