<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedCategoriesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = [
            [
                'name' => '分享',
                'description' => '分享创造，分享发现',
            ],
            [
                'name' => '教程',
                'description' => '开发技巧、推荐扩展包等',
            ],
            [
                'name' => '问答',
                'description' => '请保持码头，互帮互助',
            ],
            [
                'name' => '公告',
                'description' => '站点公告',
            ],
        ];
        // 使用 DB 类的 insert() 批量往数据表 categories 里插入数据 $catetories;
        DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // truncate 清空数据表里的所有数据
        DB::table('categories')->truncate();
    }
}
