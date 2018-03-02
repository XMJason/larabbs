<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

/**
 * 表单请求验证（FormRequest）的工作机制，利用Laravel提供的依赖注入功能，
 * 在控制器方法，如卡面我们的 udpate() 方法声明中，传参 UserRequest。
 * 这将触发表单请求类的自动验证机制，验证发生在 UserRequest 中，
 * 并使用用文件中方法 rules() 定制的规则，
 * 只有当验证通过时，才会执行控制器 update() 方法中的代码。
 * 否则抛出异常，并重定向至上一个页面，附带验证失败的信息
 */
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * authorize 方法是表单验证自带的另一个功能 —— 权限验证
     * @return bool
     */
    public function authorize()
    {
        // 暂时未使用，先 return true; 所有权限都可通过
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // name.required 必填
            // name.between 字段的大小必须在给定的 min 和 max 之间
            // name.regex 验证的字段必须与给定的正则表达式匹配
            // name.unique 验证的字段在给定的数据库表中必须是唯一的
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '用户名已被占用，请重新填写。',
            'name.regex' => '用户名只支持中英文、数字、横杠和下划线。',
            'name.between' => '用户名必须介于 3 - 25 个字符之间',
            'name.required' => '用户名不能为空',
        ];
    }
}
