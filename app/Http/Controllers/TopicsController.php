<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;

class TopicsController extends Controller
{
    public function __construct()
    {
        // 除了 index 和 show 以外的方法，都需要使用 auth 中间件进行认证
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic, User $user)
	{
		// $topics = Topic::with('user', 'category')->paginate(30);
        // $request->order 是获取URI http://larabbs.aicjs.com/topics?order=recent 中的order参数
        $topics = $topic->withOrder($request->order)->paginate(20);
        $active_users = $user->getActiveUsers();
		return view('topics.index', compact('topics', 'active_users'));
	}

    public function show(Request $request, Topic $topic)
    {
        // 当话题有Slug的时候，我们希望用户一直使用正确的，带着Slug的链接来访问。
        // 我们可以在控制器中对Slug进行判断，当条件允许的时候，我们将发送301永久重定向指令给浏览器
        // URL 矫正
        if (! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{
        // fill 方法会将传参的键值数组填充到模型的属性中
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

		// return redirect()->route('topics.show', $topic->id)->with('message', '成功创建主题！');
		return redirect()->to($topic->link())->with('message', '成功创建主题！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		// return redirect()->route('topics.show', $topic->id)->with('message', '更新成功！');
		return redirect()->to($topic->link())->with('message', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '成功删除！');
	}

    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg' => '上传失败！',
            'file_path' => ''
        ];

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg'] = '上传成功！';
                $data['success'] = true;
            }
        }
        return $data;
    }
}
