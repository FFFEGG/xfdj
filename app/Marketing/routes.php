<?php

Route::get('/', 'HomeController@index');
Route::resource('/thdzc', ThdZcController::class);
Route::resource('/group', GroupController::class);
Route::resource('/xsuser', XsUserController::class);
Route::resource('/user', UserController::class);
Route::resource('/leadersh', LeaderZcController::class);
Route::resource('/sh', MerchantController::class);
Route::resource('/merchant', shUserController::class);
Route::resource('/avtives', AvtiveController::class);
Route::resource('shsmorder', ShSmOrderController::class);
Route::resource('/orders', OrderController::class);
