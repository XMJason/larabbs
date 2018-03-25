<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.str_random(15);
        $phone = $request->phone;

        // 通过 CaptchaBuilder 创建验证码图片
        $captcha = $captchaBuilder->build();
        $expired_at = now()->addMinutes(2);
        // getPhrase 方法获取验证码文件，跟手机号一起缓存
        \Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expired_at);

        $result = [
            // 返回 captcha_key
            'captcha_key' => $key,
            // 过期时间
            'expired_at' => $expired_at->toDateTimeString(),
            // inline 方法获取的 base64 图片验证码（这里图片较小，直接以 base64格式返回图片）
            'captcha_image_content' => $captcha->inline()
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
