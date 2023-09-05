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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('username')->nullable()->comment('用户名称');
            $table->string('password')->nullable()->comment('密码');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮件有效时间');
            $table->tinyInteger('status')->nullable()->default(1)->comment('状态：1-正常，2-停用');
            $table->softDeletes()->comment('软删除，有时间数据代表删除时间');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_admin_users` comment '后台用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
};
