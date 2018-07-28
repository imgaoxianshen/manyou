<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::post('api/:version/token/user','api/:version.Token/getToken');

Route::post('api/:version/order/list','api/:version.Order/orderList');
Route::post('api/:version/order/watchorder','api/:version.Order/watchOrder');
Route::post('api/:version/order/one','api/:version.Order/getOrderOne');
Route::post('api/:version/order/orderget','api/:version.Order/orderGet');
Route::post('api/:version/order/ordersend','api/:version.Order/orderSend');
Route::post('api/:version/order/unlock','api/:version.Order/unlockMessage');

Route::post('api/:version/order','api/:version.Order/placeOrder');

Route::post('api/:version/pay/per_order','api/:version.Pay/getPerOrder');

Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');

Route::get('api/:version/TestSms/sms','api/:version.TestSms/sms');
Route::post('api/:version/TestSms/sendcode','api/:version.TestSms/sendCode');


Route::post('api/:version/user/bindmobile','api/:version.User/bindMobile');
Route::post('api/:version/user/getmobile','api/:version.User/getMobile');