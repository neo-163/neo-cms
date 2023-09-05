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
        Schema::create('verify_codes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('area_code')->nullable()->comment('电话区号');
            $table->string('phone')->nullable()->comment('电话号码');
            $table->string('code')->comment('验证码');
            $table->integer('type')->comment('1-邮箱激活码，2-手机号码激活码，3-邮箱找回密码验证码，3-手机号码找回密码验证码');
            $table->integer('status')->default(1)->comment('1-未使用,2-使用过');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_verify_codes` comment '验证码表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verify_codes');
    }
};
