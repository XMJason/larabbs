<?php

namespace App\Http\Controllers\Api;

use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // hash_equals 判断验证码是否与缓存中一致
        // hash_equals 防止时序攻击（使用 == 比较，两个字串是从第一位开始进行比较的，发现不同立即返回 false，
        // 那么通过计算返回的速度就知道了大概是哪一位开始不同的，这样就实现了电影中经常出现的按位破解密码的场景）
        // 而使用 hash_equals 比较，无论字符串是否相等，函数的时间消耗都是恒定的，这样可以有效的防止时序攻击
        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // errorUnauthorized 返回的状码是401，
            // 客户端在没有提供凭证或者提供错误的凭证时，向受保护的资源发送请求
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        // return $this->response->created();
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }

    public function me()
    {
        // Dingo\Api\Routing\Helpers 这个 trait，它提供了 user 方法，方便获取当前登录的用户，也就是 token 所对应的用户
        // $this->user() 等同于 \Auth:;guard('api')->user()
        // 我们返回的是一个单一资源，所以使用 $this->response->item，第一个参数是模型实例，第二个参数是刚刚创建的 transformer
        return $this->response->item($this->user(), new UserTransformer());
    }
}
