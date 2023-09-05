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
        Schema::create('admin_role_res', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('user_id')->comment('后台用户id');
            $table->integer('role_id')->comment('后台角色id');
        });

        \DB::statement("ALTER TABLE `cms_admin_role_res` comment '后台用户角色关系表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_role_res');
    }
};
