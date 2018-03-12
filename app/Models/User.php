<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    // 默认的 User 模型中使用了 trait —— Notifiable，它包含着一个可以用来发通知的方法 notify()
    // 此方法接收一个通知实例做参数。
    use Notifiable {
        notify as protected laravelNotify;
    }
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*
     * 用户与话题中间的关系是一对多的关系，一个用户拥有多个主题，在Eloquent中使用 hasMany() 方法进行关联。
     * 关联设置成功后，可使用 $user->topics 来获取到用户发布的所有话题数据。
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    // 一个用户可以拥有多条评论
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
}
