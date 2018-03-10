<?php

namespace App\Observers;

use App\Models\Topic;
// use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

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

    public function saved(Topic $topic)
    {
        // 写在 saving 中进行分发任务，$topic 还未在数据库中创建，所以 $topic->id 为null，导致任务执行失败

        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if (! $topic->slug) {
            // app() 允许我们使用 Laravel 服务容器，此处我们用来生成 SlugTranslateHandler 实例。
            // $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);

            // 将 Slug 翻译的调用修改为队列执行的方式
            // 推送任务到队列
            dispatch(new TranslateSlug($topic));
        }
    }
}
