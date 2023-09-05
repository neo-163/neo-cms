<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 255)->comment('标题');
            $table->string('image', 255)->nullable()->comment('图片');
            $table->text('content')->nullable()->comment('正文');
            $table->integer('user_id')->nullable()->comment('用户id');
            $table->string('url_tag', 100)->nullable()->comment('域名标签');
            $table->integer('view')->nullable()->default(0)->comment('浏览次数');
            $table->json('json')->nullable()->comment('其他内容数据');
            $table->tinyInteger('status')->nullable()->default(1)->comment('状态：1-发布，2-草稿，3-回收');
            $table->string('remarks', 2000)->nullable()->comment('备注');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_pages` comment '页面表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
