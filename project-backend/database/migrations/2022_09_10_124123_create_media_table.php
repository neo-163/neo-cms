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
        Schema::create('media', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 255)->comment('标题，可用作媒体alt名称');
            $table->string('relative_link', 255)->default('')->comment('相对链接');
            $table->string('url', 250)->unique()->nullable()->comment('初始绝对路径');
            $table->string('upload_day', 50)->nullable()->comment('上传日期'); 
            $table->string('size', 50)->nullable()->comment('换算成输出的单位');
            $table->integer('size_count')->nullable()->comment('使用字节byte做基本单位，用来计算总量大小');
            $table->integer('category_id')->nullable()->default(0)->comment('分类id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->tinyInteger('platform')->comment('类型：1-后台CMS，2-微信小程序，3-H5，4-安卓，5-IOS，6-其他');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_media` comment '媒体表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
};
