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
        Schema::create('media_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 255)->default('')->comment('标题');
            $table->string('description', 255)->default('')->comment('简介');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->integer('user_id')->nullable()->default(0)->comment('用户id');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_media_categories` comment '媒体分类表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_categories');
    }
};
