<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    public function update(User $user, Topic $topic)
    {
        // 只有当话题关联作者的ID 等于当前登录用户的ID时候才放行
        // return $topic->user_id == $user->id;
        return $user->isAuthorOf($topic);
    }

    public function destroy(User $user, Topic $topic)
    {
        // 在 多个地方使用 $topic->user_id == $user->id; 代码的可读性不高。
        // 故在 User 模型中定义 isAuthorOf 函数
        return $user->isAuthorOf($topic);
    }
}
