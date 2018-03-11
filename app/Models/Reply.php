<?php

namespace App\Models;

class Reply extends Model
{
    // 修改 Reply 模型的 $fillable 属性，我们只允许用户更改 content 字段。
    // 同时做下数据模型的关联，一条回复属于一个话题，一条回复属于一个作者所有
    protected $fillable = ['content'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
