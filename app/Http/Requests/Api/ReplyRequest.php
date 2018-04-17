<?php

namespace App\Http\Requests\Api;

// 提取公共的 authorize 函数后，不使用 use FormRequest; RepliesController 中的 $request->content 报错，但是可以正常运行
use FormRequest;

class ReplyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|min:2',
        ];
    }
}
