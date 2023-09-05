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
        Schema::create('admin_user_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->string('token', 1000)->comment('token钥匙');
            $table->string('active_time', 50)->comment('token生效时间');
            $table->string('expires_time', 50)->comment('token失效时间');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_admin_user_tokens` comment '后台用户秘钥表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_tokens');
    }
};
