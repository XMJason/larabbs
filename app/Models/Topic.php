<?php

namespace App\Models;

class Topic extends Model
{
    // 生成模型时将所有字段都罗列出来，这是很危险的。
    // 在开发数据库醋的 CRUD 功能时，都要慎重地对 $fillable 属性进行定制。
    /**
     * 在当前的情况下，以下字段将禁止用户修改
     * user_id 文章的作者
     * last_reply_user_id 最后回复的用户ID
     * order 文章排序，将会是管理员专属的功能
     * reply_count 回复数量，程序维护
     * view_count 查看数量，程序维护
     */
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    /**
     * scope
     * 这里使用了 Laravel 本地作用域。本地作用域允许我们定义通用的约束集合以便在应用中利用
     * 要定义这样的一个作用域，只需简单在对应 Eloquent模型方法前加上一个 scope 前缀，作用域总是返回 查询构建器
     * 一旦定义了作用域，则可以在查询模型时调用作用域方法。在进行调用时不需要加上scope前缀。
     * 如调用 scopeWithOrder，使用 $topic->withOrder(); 即可
     */

    public function scopeWithOrder($query, $order)
    {
        switch($order){
            case 'recent':
                $query = $this->recent();
                break;
            default:
                $query = $this->recentReplied();
                break;
        }
        // 预加载 防止 N+1 问题
        return $query->with('user', 'category');
    }

    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

    // 做了关联设定之后，可以通过 $topic->category 获取话题的分类
    public function category()
    {
        // 一个话题属于一个分类
        return $this->belongsTo(Category::class);
    }

    // 做了关联设定之后，可以通过 $topic->user 获取话题的作者
    public function user()
    {
        // 一个话题拥有一个作者
        return $this->belongsTo(User::class);
    }

    // 参数 $param 允许附加 URL 参数的设定
    public function link($param = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $param));
    }
}
