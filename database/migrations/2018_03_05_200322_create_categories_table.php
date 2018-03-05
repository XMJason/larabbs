<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            // 分类名，字符串类型，index() 添加索引 搜索优化，comment 表结构注释
            $table->string('name')->index()->comment('名称');
            // 分类的描述
            $table->text('description')->nullable()->comment('描述');
            // 分类下的帖子数量
            $table->integer('post_count')->default(0)->comment('帖子数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
