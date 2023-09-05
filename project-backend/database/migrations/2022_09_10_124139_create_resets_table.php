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
        Schema::create('resets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('area_code')->nullable()->comment('电话区号');
            $table->string('phone')->nullable()->comment('电话号码');
            $table->integer('type')->comment('1-找回密码');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_resets` comment '重设表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resets');
    }
};
