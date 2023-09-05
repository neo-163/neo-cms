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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('username')->unique()->comment('用户名');
            $table->text('description')->nullable()->comment('简介');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('area_code')->nullable()->comment('电话区号');
            $table->string('phone')->unique()->nullable()->comment('电话号码');
            $table->timestamp('phone_verified_at')->nullable()->comment('电话验证时间');
            $table->string('password')->comment('密码');
            $table->tinyInteger('status')->default(1)->comment('类型：1-一般用户，2-讲师用户，3-官方内部成员');
            $table->rememberToken();
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_migrations` comment '数据库迁移表'");
        \DB::statement("ALTER TABLE `cms_users` comment '用户表'");

        // passport模块相关的表，系统安装才会有，如create_oauth_access_tokens
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
