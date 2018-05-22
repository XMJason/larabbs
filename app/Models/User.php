<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Traits\LastActivedAtHelper;
    use Traits\ActiveUserHelper;

    use HasRoles;

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
        'name', 'phone', 'email', 'password', 'introduction', 'avatar', 'weixin_openid', 'weixin_unionid', 'registration_id'
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

    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于60，即认为已经做过加密的情况
        if (strlen($value) != 60) {
            // 不等于60，做密码加密处理
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 http 子串开头，那就是从后台上传的，需要补全URL
        if (! starts_with($path, 'http')) {
            // 拼接完整的URL
            $path = config('app.url') . '/uploads/images/avatars/'.$path;
        }
        $this->attributes['avatar'] = $path;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
