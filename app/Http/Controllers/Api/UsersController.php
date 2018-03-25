<?php

namespace App\Http\Controllers\Api;

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

        return $this->response->created();
    }
}
