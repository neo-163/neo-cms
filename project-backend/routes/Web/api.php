<?php

Route::group(['prefix' => 'web'], function () {

    // 测试 start
    Route::get('/test', '\App\Http\Controllers\Api\Web\TestController@test');
    Route::get('/test1', '\App\Http\Controllers\Admin\TestController@test_message');
    Route::post('/swoole', '\App\Http\Controllers\Api\Web\TestController@swoole');
    Route::post('/workman', '\App\Http\Controllers\Api\Web\TestController@workman');
    Route::post('/redis', '\App\Http\Controllers\Api\Web\TestController@redis');
    Route::post('/elasticsearch', '\App\Http\Controllers\Api\Web\TestController@elasticsearch');
    Route::post('/rabbitmq', '\App\Http\Controllers\Api\Web\TestController@rabbitmq');
    // 测试 end

    // 登录
    Route::post('/login', '\App\Http\Controllers\Api\Web\User\LoginController@login');
    // 注册
    Route::post('/register', '\App\Http\Controllers\Api\Web\User\LoginController@register');

    // 发送验证码-注册时
    Route::post('/sendVerificationCodeByRegister', '\App\Http\Controllers\Api\Web\User\UserController@sendVerificationCodeByRegister');
    // 发送验证码-找回密码时
    Route::post('/sendVerificationCodeByForget', '\App\Http\Controllers\Api\Web\User\UserController@sendVerificationCodeByForget');
    // 检查验证码
    Route::post('/checkVerificationCode', '\App\Http\Controllers\Api\Web\User\UserController@checkVerificationCode');
    // 忘记密码
    Route::post('/forget', '\App\Http\Controllers\Api\Web\User\UserController@forget');

    // 页面详情
    Route::post('/page/details/{url_tag}', '\App\Http\Controllers\Api\Web\PageController@details');

    // 最新文章
    Route::post('/article/newList', '\App\Http\Controllers\Api\Web\Article\ArticleController@newList');

    Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
        Route::post('/logout', '\App\Http\Controllers\Api\Web\User\LoginController@logout');
        Route::post('/info', '\App\Http\Controllers\Api\Web\User\UserController@info');

        // 首页数据
        Route::post('/index/data', '\App\Http\Controllers\IndexController@indexData');

        // 媒体 模块
        Route::post("/media/uploadOSSFile", "\App\Http\Controllers\Api\Web\MediaController@uploadOSSFile");
        Route::post("/media/deleteOSSFile", "\App\Http\Controllers\Api\Web\MediaController@deleteOSSFile");
        Route::post("/media/uploadLocal", "\App\Http\Controllers\Api\Web\MediaController@uploadLocal");

        // 支付 模块
        Route::post('/payment/wechatpay/pay', 'App\Http\Controllers\Api\Web\Payment\WechatpayController@pay');
        Route::post('/payment/alipay/pay', 'App\Http\Controllers\Api\Web\Payment\AlipayController@pay');
        Route::post('/payment/paypal/pay', 'App\Http\Controllers\Api\Web\Payment\PayPalController@pay');

        // 更细用户资料
        Route::post('/userUpdateInfo', '\App\Http\Controllers\Api\Web\User\UserController@userUpdateInfo');
        // 重设密码
        Route::post('/userChangePassword', '\App\Http\Controllers\Api\Web\User\UserController@userChangePassword');
    });
});
