<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@root')->name('root');

// Auth::routes();

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'edit']]);
// 等同于以下三行
// 显示用户个人信息页面
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');
// 显示编辑个人资料页面
// Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
// 处理 edit 页面提交的更改
// Route::patch('/users/{user}', 'UsersController@update')->name('users.update');

Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
// URI 参数 topic 是【隐性路由醋绑定】的提示，将会自动注入ID对应的话题实体。
// URI 最后一个参数表达式 {slug?}，? 意味着参数可靠，这是为了兼容我们数据库中 Slug 为空的话题数据。
// 这种写法可以同时兼容以下两种链接
// http://larabbs.aicjs.com/topics/108
// http://larabbs.aicjs.com/topics/108?slug-fan-yi-ce-shi
Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');

Route::resource('categories', 'CategoriesController', ['only' => ['show']]);

Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);

Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);

Route::get('permission-denied', 'PagesController@permissionDenied')->name('permission-denied');
