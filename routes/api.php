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
    // 增加参数，namespace，使 v1 版本的路由都会指向 App\Http\Controllers\Api
    'namespace' => 'App\Http\Controllers\Api',
    // 增加中间件serializer，参数为 array，返回的数据结构为ArraySerializer格式（默认是DataArraySerializer）
    'middleware' => ['serializer:array', 'bindings']
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api){
        // 短信验证码接口
        $api->post('verificationCodes', 'verificationCodesController@store')->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')->name('api.users.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')->name('api.captchas.store');

        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')->name('api.socials.authorizations.store');

        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');

        // 刷新 token
        $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
        // 删除 token
        $api->delete('authorizations/current', 'AuthorizationsController@destory')->name('api.authorizations.destory');
        
    });


    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        // 游客可以访问的接口
        // 分类列表接口
        $api->get('categories', 'CategoriesController@index')->name('api.categories.index');
        // 话题列表
        $api->get('topics', 'TopicsController@index')->name('api.topics.index');

        // 获取某个用户自己发布的所有话题列表
        $api->get('users/{user}/topics', 'TopicsController@userIndex')->name('api.users.topics.index');


        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function ($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')->name('api.user.show');

            // put 替换某个资源，需要提供完整的资源令牌
            // patch 部分修改资源，提供部分资源令牌

            // 编辑登录用户信息
            $api->patch('user', 'UsersController@update')->name('api.user.update');

            // 图片资源
            $api->post('images', 'ImagesController@store')->name('api.images.store');

            // 发布话题
            $api->post('topics', 'TopicsController@store')->name('api.topics.store');
            // 修改话题
            $api->patch('topics/{topic}', 'TopicsController@update')->name('api.topics.update');
            // 删除话题
            $api->delete('topics/{topic}', 'TopicsController@destory')->name('api.topics.destory');
        });
    });
});
