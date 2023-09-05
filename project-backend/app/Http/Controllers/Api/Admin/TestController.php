<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\Admin\TestService;

class TestController extends BaseController
{
    /**
     * 测试推送消息
     * @author Neo
     * @return mixed
     */
    public function test_message()
    {
        // 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
        // 推送的数据，包含uid字段，表示是给这个uid推送
        //$uid = empty($uidList) ? 0 : json_decode($uidList, true);
        $data = [
            'uid' => 'all', //$uid
            'message' => "你有一条新消息!",
            'data' => array('name' => 'mr chen', 'sex' => '18')
        ];
        // 发送数据，注意5679端口是Text协议的端口，Text协议需要在数据末尾加上换行符
        fwrite($client, json_encode($data) . "\n");
        // 读取推送结果
        //        echo fread($client, 8192);
        return fread($client, 8192);
    }

    /**
     * 查询文章，和依据封面图id关联封面图数据
     * @author Neo
     * @return mixed
     */
    public function articleList()
    {
        return TestService::articleList();
    }
}
