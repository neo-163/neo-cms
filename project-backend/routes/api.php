<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 目前采用自定义路由，自动定位路由虽然更节省代码量，但是有以下缺点：
// 1.安全性有问题，随意使用get方法，可能触发敏感的功能
// 2.即使设置了指定路由为post等其他方法，甚至关闭指定路由，但是规则不一致，容易混乱，而且一不小心，还是倾向回到上面的安全问题

Route::group(['middleware' => ['cors', /*'setting'*/]], function () {
    include_once('Admin/api.php'); // 后台接口
    include_once('Web/api.php'); // 网页端接口
});
