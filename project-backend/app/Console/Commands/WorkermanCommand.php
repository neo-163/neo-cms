<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;

class WorkermanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workerman:command {action} {d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Workerman Server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        global $worker;

        $worker = new Worker('websocket://' . env('HOST', '127.0.0.1') . ':' . env('WEBSOCKET_PORT', '1234'));
        // 这里进程数必须设置为1
        $worker->count = 1;

        // worker进程启动后建立一个内部通讯端口
        $worker->onWorkerStart = function ($worker) {
            // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
            $inner_text_worker = new Worker('Text://' . env('HOST', '0.0.0.0') . ':5678');
            $inner_text_worker->onMessage = function ($connection, $buffer) {
                global $worker;
                // $data数组格式，里面有uid，表示向那个uid的页面推送数据
                $data = json_decode($buffer, true);
                $uid = $data['uid'];
                // 全局广播
                $ret = array();
                if ($uid == 'all') {
                    $ret[] = broadcast(json_encode($data));
                } else {
                    // 通过workerman，向uid的页面推送数据
                    $uids = $data['uid'];
                    $arr_uid = explode(',', $uids);
                    foreach ($arr_uid as $v) { //多个用户推送
                        $ret[] = sendMessageByUid($v, $buffer);
                    }
                    //                    $ret = sendMessageByUid($uid, $buffer);
                }
                $path = app_path();
                //记录日志
                file_put_contents($path . '/aa.txt', "\r\n 接收到 date: " . date('Y-m-d H:i:s') . ' data_json:' . var_export($ret, true), FILE_APPEND);
                if (!empty($ret)) {
                    $ret = true;
                }
                //                $uid = $data['uid'];
                //                // 通过workerman，向uid的页面推送数据
                //                $ret = sendMessageByUid($uid, $buffer);
                // 返回推送结果
                $connection->send($ret ? 'ok' : 'fail');
            };
            $inner_text_worker->listen();
        };
        // 新增加一个属性，用来保存uid到connection的映射
        $worker->uidConnections = array();
        // 当有客户端发来消息时执行的回调函数
        $worker->onMessage = function ($connection, $data) use ($worker) {
            // 判断当前客户端是否已经验证,既是否设置了uid
            if (!isset($connection->uid)) {
                // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
                $connection->uid = $data;
                /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
                 * 实现针对特定uid推送数据
                 */
                $worker->uidConnections[$connection->uid] = $connection;
                return;
            }
        };

        // 当有客户端连接断开时
        $worker->onClose = function ($connection) use ($worker) {
            global $worker;
            if (isset($connection->uid)) {
                // 连接断开时删除映射
                unset($worker->uidConnections[$connection->uid]);
            }
        };

        // 向所有验证的用户推送数据
        function broadcast($message)
        {
            global $worker;
            foreach ($worker->uidConnections as $connection) {
                $connection->send($message);
            }
            return true;
        }

        // 针对uid推送数据
        function sendMessageByUid($uid, $message)
        {
            global $worker;
            if (isset($worker->uidConnections[$uid])) {
                $connection = $worker->uidConnections[$uid];
                $connection->send($message);
                return true;
            }
            return false;
        }

        // 进程启动后设置一个每秒运行一次的定时器
        // $worker->onWorkerStart = function ($worker) {
        //     Timer::add(CHECK_HEARTBEAT_TIME, function () use ($worker) {
        //         $time_now = time();
        //         foreach ($worker->connections as $connection) {
        //             // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
        //             if (empty($connection->lastMessageTime)) {
        //                 $connection->lastMessageTime = $time_now;
        //                 continue;
        //             }

        //             // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
        //             if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
        //                 $connection->close();
        //             }
        //         }
        //     });
        // };

        // 运行所有的worker
        Worker::runAll();
    }
}
