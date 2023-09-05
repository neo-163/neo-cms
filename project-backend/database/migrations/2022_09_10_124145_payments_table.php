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
        Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('payment_id')->comment('账单id');
            $table->string('payer_id')->comment('用户id');
            $table->string('payer_email')->comment('邮箱');
            $table->float('amount', 10, 2)->comment('价格');
            $table->string('currency')->comment('货币单位');
            $table->string('payment_status')->comment('状态');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `cms_payments` comment '支付表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
