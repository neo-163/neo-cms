<?php

Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', '\App\Http\Controllers\Api\Admin\User\LoginController@login');
    Route::post('/register', '\App\Http\Controllers\Api\Admin\User\LoginController@register');

    Route::group(['middleware' => 'admin', 'prefix' => 'auth'], function () {
        Route::post('/logout', '\App\Http\Controllers\Api\Admin\User\LoginController@logout');
        Route::post('/info', '\App\Http\Controllers\Api\Admin\User\UserController@info');

        // 首页数据
        Route::post('/index/data', '\App\Http\Controllers\IndexController@indexData');

        // 用户模块
        Route::post('/user/list', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminController@list');
        Route::post('/user/details', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminController@details');
        Route::post('/user/register', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminController@register');
        Route::post('/user/edit', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminController@edit');
        Route::delete('/user/delete', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminController@delete');

        // 角色模块
        Route::post('/role/list', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminRoleController@list');
        Route::post('/role/details', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminRoleController@details');
        Route::post('/role/store', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminRoleController@store');
        Route::delete('/role/delete', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminRoleController@delete');

        // 规则权限模块
        Route::post('/permission/list', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminPermissionController@list');
        Route::post('/permission/details', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminPermissionController@details');
        Route::post('/permission/store', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminPermissionController@store');
        Route::delete('/permission/delete', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminPermissionController@delete');

        // 规则权限的分级选项列表
        Route::post('/permission/selects', '\App\Http\Controllers\Api\Admin\AdminRolePermission\AdminPermissionController@selects');

        // 页面模块
        Route::post('/page/list', '\App\Http\Controllers\Api\Admin\Page\PageController@list');
        Route::post('/page/details', '\App\Http\Controllers\Api\Admin\Page\PageController@details');
        Route::post('/page/create', '\App\Http\Controllers\Api\Admin\Page\PageController@create');
        Route::post('/page/edit', '\App\Http\Controllers\Api\Admin\Page\PageController@edit');
        Route::delete('/page/delete', '\App\Http\Controllers\Api\Admin\Page\PageController@delete');

        // 文章模块
        Route::post('/article/list', '\App\Http\Controllers\Api\Admin\Article\ArticleController@list');
        Route::post('/article/details', '\App\Http\Controllers\Api\Admin\Article\ArticleController@details');
        Route::post('/article/create', '\App\Http\Controllers\Api\Admin\Article\ArticleController@create');
        Route::post('/article/edit', '\App\Http\Controllers\Api\Admin\Article\ArticleController@edit');
        Route::delete('/article/delete', '\App\Http\Controllers\Api\Admin\Article\ArticleController@delete');

        // 媒体模块
        Route::post("/media/uploadOSSFile", "\App\Http\Controllers\Api\Admin\Media\MediaController@uploadOSSFile");
        Route::delete("/media/deleteOSSFile", "\App\Http\Controllers\Api\Admin\Media\MediaController@deleteOSSFile");

        Route::post("/media/uploadLocal", "\App\Http\Controllers\Api\Admin\Media\MediaController@uploadLocal");
        Route::delete("/media/deleteLocal", "\App\Http\Controllers\Api\Admin\Media\MediaController@deleteLocal");

        Route::post('/media/list', '\App\Http\Controllers\Api\Admin\Media\MediaController@list');
        Route::post('/media/details', '\App\Http\Controllers\Api\Admin\Media\MediaController@details');
        Route::post('/media/create', '\App\Http\Controllers\Api\Admin\Media\MediaController@create');
        Route::post('/media/edit', '\App\Http\Controllers\Api\Admin\Media\MediaController@edit');

        // 媒体分类模块
        Route::post('/mediaCategory/list', '\App\Http\Controllers\Api\Admin\Media\MediaCategoryController@list');
        Route::post('/mediaCategory/details', '\App\Http\Controllers\Api\Admin\Media\MediaCategoryController@details');
        Route::post('/mediaCategory/create', '\App\Http\Controllers\Api\Admin\Media\MediaCategoryController@create');
        Route::post('/mediaCategory/edit', '\App\Http\Controllers\Api\Admin\Media\MediaCategoryController@edit');
        Route::delete('/mediaCategory/delete', '\App\Http\Controllers\Api\Admin\Media\MediaCategoryController@delete');
    });
});
