<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Reply;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reply $reply)
    {
        // 注入回复实体，方便 toDatabase 方法中的使用
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     * 每个通知类都有个 via() 方法，它决定了通知在哪个频道上发送
     * 我们写上 database 数据库来作为通知频道
     * 使用数据库通知，需要定义 toDatabase，这个方法接收 $notifiable 实例参数并返回一个普通的PHP数组
     * 这个返回的数组将被转成JSON格式并存储到通知数据库表的data字段中
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // 开户通知的频道
        // 再开启 mail 频道，同时需要增加 toMail 方法
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        $topic = $this->reply->topic;
        $link = $topic->link(['#reply' . $this->reply->id]);

        // 存入数据库里的数据
        return [
            'reply_id' => $this->reply->id,
            'reply_content' => $this->reply->content,
            'user_id' => $this->reply->user->id,
            'user_name' => $this->reply->user->name,
            'user_avatar' => $this->reply->user->avatar,
            'topic_link' => $link,
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
        ];
    }

    public function toMail($notifiable)
    {
        $url = $this->reply->topic->link();
        return (new MailMessage)->line('你的话题有新回复！')->action('查看回复', $url);
    }
}
