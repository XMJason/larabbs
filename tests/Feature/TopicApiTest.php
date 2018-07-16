<?php
/**
 * Feature 功能测试
 * 是针对你代码中大部分的代码来进行测试，包括几个对象的相互作用，甚至是一个完整的 HTTP 请求 JSON 实例
 */

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\ActingJWTUser;

class TopicApiTest extends TestCase
{
    use ActingJWTUser;

    protected $user;

    /**
     * setUP 方法会在测试开始前执行，在其中先创建一个用户，测试会以该用户的身份进行测试
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * testStoreTopic 就是一个测试用户，测试发布话题。
     */
    public function testStoreTopic()
    {
        $data = ['category_id' => 1, 'body' => 'test body', 'title' => 'test title'];

        // 为用户生成 Token 以及设置 Authorization，不仅修改话题，删除话题会使用，以后编写其它功能的测试用例一样会使用，所以我们进行一下封装
        // 在 tests/Traits 下创建 ActingJWTUser.php

        // $token = \Auth::guard('api')->fromUser($this->user);

        // 使用 $this->json 可以方便的模拟各种 HTTP 请求。
        // 第一个参数：请求的方法，发布话题使用的是 POST 方法
        // 第二个参数：请求的地址，请求 /api/topics
        // 第三个参数：请求参数，传入 category_id, body, title，这三个必填参数
        // 第四个参数：请求 Header，可以直接设置 header，也可以利用 withHeaders 方法达到同样的目的
        // $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->json('POST', '/api/topics', $data);


        // Token 封装后
        $response = $this->JWTActingAs($this->user)->json('POST', '/api/topics', $data);

        $assertData = [
            'category_id' => 1,
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => clean('test body', 'user_topic_body'),
        ];

        $response->assertStatus(201)->assertJsonFragment($assertData);
    }

    /**
     * 测试修改话题
     */
    public function testUpdateTopic()
    {
        $topic = $this->makeTopic();

        $editData = ['category_id' => 2, 'body' => 'edit body', 'title' => 'edit title'];

        $response = $this->JWTActingAs($this->user)->json('PATCH', '/api/topics/'.$topic->id, $editData);

        $assertData = [
            'category_id' => 2,
            'user_id' => $this->user->id,
            'title' => 'edit title',
            'body' => clean('edit body', 'user_topic_body'),
        ];

        $response->assertStatus(200)->assertJsonFragment($assertData);
    }

    /**
     * @return mixed
     *
     * 增加修改话题的测试用例，首先为该用户创建一个话题
     */
    protected function makeTopic()
    {
        return factory(Topic::class)->create([
            'user_id' => $this->user->id,
            'category_id' => 1,
        ]);
    }

    /**
     * 测试查看话题
     *
     * 先创建一个话题，然后访问话题详情接口
     */
    public function testShowTopic()
    {
        $topic = $this->makeTopic();

        $response = $this->json('GET', '/api/topics/'.$topic->id);

        $assertData = [
            'category_id' => $topic->category_id,
            'user_id' => $topic->user_id,
            'title' => $topic->title,
            'body' => $topic->body,
        ];

        $response->assertStatus(200)->assertJsonFragment($assertData);

    }

    /**
     * 话题列表
     */
    public function testIndexTopic()
    {
        $response = $this->json('GET', '/api/topics');

        $response->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    /**
     * 测试删除话题
     */
    public function testDeleteTopic()
    {
        $topic = $this->makeTopic();

        $response = $this->JWTActingAs($this->user)->json('DELETE', '/api/topics/'.$topic->id);
        $response->assertStatus(204);

        $response = $this->json('GET', '/api/topics/'.$topic->id);
        $response->assertStatus(404);
    }


}
