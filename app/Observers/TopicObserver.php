<?php

namespace App\Observers;

use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function saving(Topic $topic)
    {
        // 对 topic 的 body 内容进行过滤
        $topic->body = clean($topic->body, 'user_topic_body');

        // make_excerpt 是自定义的辅助方法
        $topic->excerpt = make_excerpt($topic->body);
    }
}
