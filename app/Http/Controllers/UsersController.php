<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 表单请求 UserRequest 是 Laravel 框架提供的用户表单数据验证方案
     * 此方案相比手工调用 validator 来说，能处理更为复杂的验证逻辑，更加适用于大型程序。
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
