<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\BaseController;
use App\Jobs\SendEmail;

class TestController extends BaseController
{
    /**
     * 测试
     * @author Neo
     * @return mixed
     */
    public function test()
    {
        return 'api-test';
    }

    /**
     * 队列测试
     * @author Neo
     * @return mixed
     */
    public function rabbitmq()
    {
        $data = ['1', 'test1'];
        // send_email 对应的队列名
        SendEmail::dispatch($data)->onConnection('rabbitmq')->onQueue('send_email');
        // 以下配置是rabbitmq 路由模式(direct) 广播模式(topic)
        // SendEmail::dispatch($user)->onConnection('rabbitmq');

        return 1;
    }
}
