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
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 150)->comment('标题');
            $table->string('route', 2000)->nullable()->comment('路由');
            $table->integer('parent')->nullable()->default(0)->comment('父级');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->tinyInteger('status')->nullable()->default(1)->comment('状态：1-启用，2-停用');
            $table->string('description', 255)->nullable()->comment('描述');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_admin_permissions` comment '后台用户权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_permissions');
    }
};
