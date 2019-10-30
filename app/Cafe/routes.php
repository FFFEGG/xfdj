<?php

Route::get('/', 'HomeController@index');
Route::get('/getCafelist', function (){
   return \App\Restaurant::get(['id','title as text']);
});

Route::get('/getCafecatelist', function (\Illuminate\Http\Request $request){
    $provinceId = $request->get('q');
    return \App\CafeCate::where('cafe_id', $provinceId)->get(['id', DB::raw('name as text')]);
});


Route::resource('/cafelist', RestaurantController::class);
Route::resource('/cafe_cate', CafeCateController::class);
Route::resource('/cafe_goods', CafeGoodsController::class);
Route::resource('/cafeimgs', CafeImgsController::class);

