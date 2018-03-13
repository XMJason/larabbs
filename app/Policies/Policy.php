<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    // before 方法会在策略中其它所有方法之前执行
    // 返回 true 直接通过授权
    // 返回 false 拒绝用户所有的授权
    // 返回 null，则通过其它策略方法来决定授权通过与否
    public function before($user, $ability)
	{
        // 如果用户拥有管理员内容的权限的话，即授权通过
        if ($user->can('manage_contents')) {
            return true;
        }
	}
}
